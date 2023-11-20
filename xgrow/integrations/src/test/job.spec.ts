/* eslint-disable @typescript-eslint/no-unused-vars */
/* eslint-disable import/first */
const process = jest.fn()

import { IProcessable } from '../contracts/processable'
import { Expo } from '../services/expo'
import { Job, Payload, JobResponse } from '../job'
import WinstonLog from '../providers/winston'
import { ApmSpan } from '../decorators/apm-spam'

jest.mock('../providers/winston', () => ({
  getInstance: jest.fn().mockReturnValue({
    debug: jest.fn(),
    error: jest.fn(),
    logJobResponse: jest.fn(),
    info: jest.fn()
  }),
}))

jest.mock('../services/expo', () => {
  return {
    Expo: jest.fn().mockImplementation(() => ({
      process
    }))
  }
})

jest.mock('../decorators/apm-spam', () => ({
  ApmSpan: jest.fn()
}))

describe('Job', () => {
  let mockPayload: Payload
  let mockResponse: JobResponse

  beforeEach(() => {
    mockPayload = {
      header: {
        date: '2022-01-01',
        app: {
          id: 1,
          app_id: 1,
          platform_id: 'platform_id',
          event: 'event',
          action: 'bindPushNotification',
          planIds: [1, 2, 3],
          integration: {
            id: 1,
            type: 'expo',
            api_key: 'api_key',
            api_account: 'api_account',
            api_webhook: 'api_webhook',
            api_secret: 'api_secret',
            signature: 'signature',
            metadata: { key: 'value' }
          }
        }
      },
      payload: {
        data: { key: 'value' }
      }
    }

    mockResponse = {
      payload: mockPayload,
      jobId: 'jobId',
      error: null,
      response: {
        status: 200,
        data: { key: 'value' },
        statusText: 'anystatus',
        config: {},
        headers: {}
      },
      isRetry: false
    }
  })

  describe('process', () => {
    it('should call process on the choosed integration', async () => {
      await Job.process(mockPayload)

      expect(process).toHaveBeenCalled()
    })
  })

  describe('resolve',() => {
    it('should log response with debug', async () => {
      const logger = WinstonLog.getInstance()

      await Job.resolve(mockResponse)

      expect(logger.logJobResponse).toHaveBeenCalled()
    })
  })

  describe('reject', () => {
    it('should call error on the logger', async () => {
      const logger = WinstonLog.getInstance()

      await Job.reject(mockResponse)

      expect(logger.logJobResponse).toHaveBeenCalled()
    })
  })
})
