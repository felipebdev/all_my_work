/* eslint-disable node/no-callback-literal */
/* eslint-disable import/first */

const loggerMock = {
  info: jest.fn(),
  error: jest.fn(),
  log: jest.fn(),
  emergency: jest.fn(),
  warning: jest.fn(),
  debug: jest.fn()
}

const logFactoryMock = {
  handleResponse: jest.fn(() => ({})),
  createLog: jest.fn(async () => ({}))
}

const bullMqMock = {
  Queue: jest.fn(() => ({
    on: jest.fn()
  })),
  Worker: jest.fn((queueName, cb) => {
    // eslint-disable-next-line node/no-callback-literal
    cb({ id: 'any', data: { any: 'any', header: { app: { integration: { type: 'any' } } } }, attemptsMade: 1 , opts: { attempts: 0 } })
    return {
      on: jest.fn((event, cb) => {
        switch (event) {
          case 'completed':
            cb({ returnvalue: 'any' })
            break
          case 'failed':
            cb({}, new Error())
            break
          default:
            break
        }
      })
    }
  }),
  UnrecoverableError: jest.fn(),
}

/* eslint-disable @typescript-eslint/no-unused-vars */
import { Queue, Worker, Job, UnrecoverableError } from 'bullmq'
import BullMqProvider from '../bullmq'
import { LogFactory } from '../../database/mongodb/factories'
import WinstonLog from '../winston'
import { EventEmitter } from 'events'
import IORedis from 'ioredis'
import apm from 'elastic-apm-node'
import { ApmSpan } from '../../decorators/apm-spam'

jest.mock('winston', () => ({
  Logger: loggerMock,
  createLogger: jest.fn(() => loggerMock),
  format: {
    json: jest.fn(),
    printf: jest.fn()
  },
  transports: {
    Console: jest.fn()
  }
}))

jest.mock('ioredis')

jest.mock('../../decorators/apm-spam', () => ({
  ApmSpan: jest.fn()
}))

jest.mock('bullmq', () => {
  return bullMqMock
})

jest.mock('elastic-apm-node', () => ({
  startTransaction: jest.fn(() => ({
    result: null,
    end: jest.fn()
  })),
  captureError: jest.fn()
}))

jest.mock('../../database/mongodb/factories', () => {
  return {
    LogFactory: jest.fn().mockImplementation(() => {
      return {
        handleResponse: logFactoryMock.handleResponse,
        createLog: logFactoryMock.createLog
      }
    })
  }
})

const dummyCallbacks = {
  dummyCallable: jest.fn(async() => ({ any: 'callable' })),
  dummyResolve: jest.fn(async() => ({ any: 'resolve' })),
  dummyReject: jest.fn(async() => ({ any: 'reject' }))
}

describe('BullMqProvider', () => {
  let bullMq

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  it('should initialize service correctly', () => {
    bullMq = BullMqProvider.getInstance()
    expect(bullMq).toBeDefined()
  })

  describe('consume', () => {
    it('should call callable and resolve in case of success', async() => {
      await bullMq.consume(
        dummyCallbacks.dummyCallable,
        dummyCallbacks.dummyResolve,
        dummyCallbacks.dummyReject
      )
      expect(logFactoryMock.handleResponse).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledWith({ any: 'any', header: { app: { integration: { type: 'any' } } } })
    })

    it('should call callable and throw error in case of failure', async() => {
      let err
      bullMqMock.Worker.mockImplementationOnce((queueName, cb) => {
        // eslint-disable-next-line node/no-callback-literal
        cb({ id: 'any', data: { any: 'any', header: { app: { integration: { type: 'any' } } } }, attemptsMade: 1 }).catch((error) => { err = error })
        return {
          on: jest.fn()
        }
      })
      dummyCallbacks.dummyCallable.mockImplementationOnce(() => {
        throw new Error('anyerror')
      })
      await bullMq.consume(
        dummyCallbacks.dummyCallable,
        dummyCallbacks.dummyResolve,
        dummyCallbacks.dummyReject
      )
      expect(logFactoryMock.handleResponse).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledWith({ any: 'any', header: { app: { integration: { type: 'any' } } } })
      expect(err).toStrictEqual({ error: 'anyerror', isRetry: false, jobId: 'any', payload: { any: 'any', header: { app: { integration: { type: 'any' } } } }, response: {} }
      )
    })

    it('should call callable and reassign attempts in case of invalid payload', async() => {
      let err
      bullMqMock.Worker.mockImplementationOnce((queueName, cb) => {
        // eslint-disable-next-line node/no-callback-literal
        cb({ id: 'any', data: { any: 'any', header: { app: { integration: { type: 'any' } } } }, attemptsMade: 1 }).catch((error) => { err = error })
        return {
          on: jest.fn()
        }
      })
      dummyCallbacks.dummyCallable.mockImplementationOnce(() => {
        throw new Error('Payload is invalid')
      })
      await bullMq.consume(
        dummyCallbacks.dummyCallable,
        dummyCallbacks.dummyResolve,
        dummyCallbacks.dummyReject
      )

      expect(dummyCallbacks.dummyCallable).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledWith({ any: 'any', header: { app: { integration: { type: 'any' } } } })
    })

    it('should call callable and reject with default message', async() => {
      let err
      bullMqMock.Worker.mockImplementationOnce((queueName, cb) => {
        // eslint-disable-next-line node/no-callback-literal
        cb({ id: 'any', data: { any: 'any', header: { app: { integration: { type: 'any' } } } }, attemptsMade: 1 }).catch((error) => { err = error })
        return {
          on: jest.fn()
        }
      })
      dummyCallbacks.dummyCallable.mockImplementationOnce(() => {
        throw new Error()
      })
      await bullMq.consume(
        dummyCallbacks.dummyCallable,
        dummyCallbacks.dummyResolve,
        dummyCallbacks.dummyReject
      )
      expect(logFactoryMock.handleResponse).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledWith({ any: 'any', header: { app: { integration: { type: 'any' } } } })
      expect(err).toStrictEqual({
        error: 'Error not specified',
        isRetry: false,
        jobId: 'any',
        payload: {
          any: 'any',
          header: { app: { integration: { type: 'any' } } }
        },
        response: {},
      }
      )
    })

    it('should call callable and resolve with undefined response', async() => {
      dummyCallbacks.dummyCallable.mockImplementationOnce(() => undefined)
      await bullMq.consume(
        dummyCallbacks.dummyCallable,
        dummyCallbacks.dummyResolve,
        dummyCallbacks.dummyReject
      )
      expect(logFactoryMock.handleResponse).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledTimes(1)
      expect(dummyCallbacks.dummyCallable).toBeCalledWith({ any: 'any', header: { app: { integration: { type: 'any' } } } })
    })

    it('should call resolveFn on job complete event', async () => {
      await bullMq.consume(
        dummyCallbacks.dummyCallable,
        dummyCallbacks.dummyResolve,
        dummyCallbacks.dummyReject
      )
      await bullMqMock.Worker('any', () => {}).on('completed', () => {})
      expect(dummyCallbacks.dummyResolve).toBeCalledTimes(1)
    })
  })
})
