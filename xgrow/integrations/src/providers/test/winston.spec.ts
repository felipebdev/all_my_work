import WinstonLog from '../winston'
import { JobResponse } from '../../job'
import { AxiosResponse } from 'axios'

describe('Winston', () => {
  let winston

  beforeEach(() => {
    winston = WinstonLog.getInstance()
    jest.clearAllMocks()
    jest.clearAllTimers()
  })

  it('should log with debug level', () => {
    const message = 'Debug message'
    const payload = { data: 'Debug payload' }
    const spy = jest.spyOn(winston.logger, 'log')
    winston.debug(message, payload)
    expect(spy).toHaveBeenCalledWith('debug', message, payload)
  })

  it('should log with info level', () => {
    const message = 'Info message'
    const payload = { data: 'Info payload' }
    const spy = jest.spyOn(winston.logger, 'log')
    winston.info(message, payload)
    expect(spy).toHaveBeenCalledWith('info', message, payload)
  })

  it('should log with warning level', () => {
    const message = 'Warning message'
    const payload = { data: 'Warning payload' }
    const spy = jest.spyOn(winston.logger, 'log')
    winston.warning(message, payload)
    expect(spy).toHaveBeenCalledWith('warning', message, payload)
  })

  it('should log with error level', () => {
    const message = 'Error message'
    const payload = { data: 'Error payload' }
    const spy = jest.spyOn(winston.logger, 'log')
    winston.error(message, payload)
    expect(spy).toHaveBeenCalledWith('error', message, payload)
  })

  it('should log with emergency level', () => {
    const message = 'Emergency message'
    const payload = { data: 'Emergency payload' }
    const spy = jest.spyOn(winston.logger, 'log')
    winston.emergency(message, payload)
    expect(spy).toHaveBeenCalledWith('emergency', message, payload)
  })

  describe('logJobResponse method', () => {
    const mockPayloadData: JobResponse = {
      payload: {
        header: {
          app: {
            event: 'event',
            platform_id: 'platform_id',
            planIds: [1],
            integration: { type: 'type', id: 1 },
            action: 'action',
            app_id: 1234,
            id: 1234,
          },
          date: '12-24-2022',
          correlation_id: '1234567810'
        },
        payload: {
          data: {
            subscriber_id: 1234
          }
        }
      },
      response: { status: 200 } as unknown as AxiosResponse,
      isRetry: false
    }

    it('should log a debug message', () => {
      const logSpy = jest.spyOn(winston, 'debug')
      winston.logJobResponse('DEBUG', 'debug message', {
        ...mockPayloadData,
        payload: {
          ...mockPayloadData.payload,
          payload: {
            data: {}
          }
        }
      })
      expect(logSpy).toHaveBeenCalled()
    })

    it('should log a debug message with custom uuid', () => {
      const logSpy = jest.spyOn(winston, 'debug')
      winston.logJobResponse('DEBUG', 'debug message', {
        ...mockPayloadData,
        payload: {
          ...mockPayloadData.payload.payload,
          header: {
            ...mockPayloadData.payload.header,
            correlation_id: undefined
          },
          payload: {
            data: {}
          }
        }
      })
      expect(logSpy).toHaveBeenCalled()
    })

    it('should log an info message', () => {
      const logSpy = jest.spyOn(winston, 'info')
      winston.logJobResponse('INFO', 'info message', mockPayloadData)
      expect(logSpy).toHaveBeenCalled()
    })

    it('should log a warning message', () => {
      const logSpy = jest.spyOn(winston, 'warning')
      winston.logJobResponse('WARNING', 'warning message', mockPayloadData)
      expect(logSpy).toHaveBeenCalled()
    })

    it('should log an error message', () => {
      const logSpy = jest.spyOn(winston, 'error')
      winston.logJobResponse('ERROR', 'error message', mockPayloadData)
      expect(logSpy).toHaveBeenCalled()
    })

    it('should log an emergency message', () => {
      const logSpy = jest.spyOn(winston, 'emergency')
      winston.logJobResponse('EMERGENCY', 'emergency message', mockPayloadData)
      expect(logSpy).toHaveBeenCalled()
    })
  })
})
