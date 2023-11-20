/* eslint-disable @typescript-eslint/no-unused-vars */
import dotenv from 'dotenv'
import env from '../config/env'
import BullMqAdapter from '../adapters/bullmq-adapter'
import ILogable from '../contracts/logable'
import WinstonLog from '../providers/winston'
import Consumer from '../consumer'
import Mongodb from '../database/mongodb'
import { App, bootstrap } from '../app'
import { SchedulerProvider } from '../providers/scheduler'
import { ApmSpan } from '../decorators/apm-spam'

jest.mock('../config/apm', () => ({}))

jest.mock('elastic-apm-node', () => ({
  startTransaction: jest.fn(() => ({
    result: null,
    end: jest.fn()
  })),
  captureError: jest.fn()
}))

jest.mock('../decorators/apm-spam', () => ({
  ApmSpan: jest.fn()
}))

jest.mock('dotenv', () => ({
  config: jest.fn(),
}))

const instanceBullMqMock = jest.fn()

jest.mock('../adapters/bullmq-adapter', () => {
  return jest.fn().mockImplementation(() => ({
    instance: instanceBullMqMock,
  }))
})

jest.mock('../providers/winston', () => ({
  getInstance: jest.fn().mockReturnValue({
    debug: jest.fn(),
    error: jest.fn(),
    info: jest.fn(),
    logJobResponse: jest.fn(),
    emergency: jest.fn()
  }),
}))

jest.mock('../providers/scheduler', () => ({
  SchedulerProvider: {
    getInstance: jest.fn().mockReturnValue({
      initHealthLogger: jest.fn()
    }),
  }
}))

jest.mock('../consumer', () => ({
  init: jest.fn(),
}))

const mongoDbConnectMock = jest.fn()

jest.mock('../database/mongodb', () => {
  return jest.fn().mockImplementation(() => ({
    connect: mongoDbConnectMock
  }))
})

jest.mock('../contracts/consumable', () => {})

describe('bootstrap', () => {
  beforeEach(() => {
    jest.clearAllMocks()
    jest.clearAllTimers()
  })

  it('should setup mongodb', async () => {
    const logger = WinstonLog.getInstance()
    const app = new App()
    await app.setupMongodb()
    expect(logger.info).toBeCalledWith('✅ Mongodb connected')
  })

  it('should setup BullMq', async() => {
    const logger = WinstonLog.getInstance()
    const app = new App()
    await app.setupBullMq()
    expect(instanceBullMqMock).toBeCalledTimes(1)
    expect(logger.info).toBeCalledWith('✅ BullMQ connected')
  })

  it('log error if MongoDb fails', async () => {
    const logger = WinstonLog.getInstance()
    const app = new App()
    mongoDbConnectMock.mockImplementationOnce(() => {
      throw new Error('anyerror')
    })
    await expect(app.setupMongodb()).rejects.toThrowError(expect.any(Error))
  })

  it('log error if setupBullMq fails', async () => {
    const logger = WinstonLog.getInstance()
    const app = new App()
    instanceBullMqMock.mockImplementationOnce(() => {
      throw new Error('anyerror')
    })
    await expect(app.setupBullMq()).rejects.toThrowError(expect.any(Error))
  })

  it('should log bootstrap error if mongodb fails', async () => {
    const logger = WinstonLog.getInstance()
    mongoDbConnectMock.mockImplementationOnce(() => {
      throw new Error('anyerrormongo')
    })
    await expect(bootstrap()).rejects.toThrowError('❌ MongoDB error: anyerrormongo')
  })

  it('should log bootstrap error if bullmq fails', async () => {
    const logger = WinstonLog.getInstance()
    instanceBullMqMock.mockImplementationOnce(() => {
      throw new Error('anymqerror')
    })
    await expect(bootstrap()).rejects.toThrowError('❌ BullMQ error: anymqerror')
  })

  it('should log bootstrap correctly and init BullMq consumer', async () => {
    const logger = WinstonLog.getInstance()
    await expect(bootstrap()).resolves.toBeUndefined()
    expect(logger.info).toHaveBeenNthCalledWith(1, '✅ Environment configured')
    expect(logger.info).toHaveBeenNthCalledWith(2, '🚀 Starting application')
    expect(logger.info).toHaveBeenNthCalledWith(3, '✅ Mongodb connected')
    expect(logger.info).toHaveBeenNthCalledWith(4, '✅ BullMQ connected')
    expect(Consumer.init).toBeCalledTimes(1)
  })
})
