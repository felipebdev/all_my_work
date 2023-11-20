// eslint-disable-next-line @typescript-eslint/no-unused-vars
import './config/apm'
import { validateEnvs } from './config/env'
import BullMqAdapter from './adapters/bullmq-adapter'
import ILogable from './contracts/logable'
import WinstonLog from './providers/winston'
import Consumer from './consumer'
import Mongodb from './database/mongodb'
import IConsumable from './contracts/consumable'
import { SchedulerProvider } from './providers/scheduler'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ApmSpan } from './decorators/apm-spam'
import apm from 'elastic-apm-node'

const logger: ILogable = WinstonLog.getInstance()

export class App {
  constructor () {
    validateEnvs()
    logger.info('✅ Environment configured')
    logger.info('🚀 Starting application')
  }

  @ApmSpan('mongodb setup')
  async setupMongodb(): Promise<void> {
    try {
      const mongodbAdapter = new Mongodb()
      await mongodbAdapter.connect()
      logger.info('✅ Mongodb connected')
    } catch (error) {
      throw new Error(`❌ MongoDB error: ${error.message}`)
    }
  }

  @ApmSpan('bullMq setup')
  async setupBullMq(): Promise<IConsumable> {
    try {
      const bullMqAdapterInstance = new BullMqAdapter().instance()
      logger.info('✅ BullMQ connected')
      return bullMqAdapterInstance
    } catch (error) {
      throw new Error(`❌ BullMQ error: ${error.message}`)
    }
  }

  setupHealthLogger(): void {
    const scheduler = SchedulerProvider.getInstance()
    scheduler.initHealthLogger()
  }
}

export const bootstrap = async (): Promise<void> => {
  const apmTransaction = await apm.startTransaction('App Bootstrap', 'setup')
  const app = new App()
  app.setupHealthLogger()

  await app.setupMongodb()
  const bullInstance = await app.setupBullMq()

  Consumer.init(bullInstance)

  apmTransaction.result = 'success'
  await apmTransaction.end()
}

/* istanbul ignore next */
bootstrap()
  .then(() => logger.info('🚀 Bootstrap done'))
  .catch(error => logger.emergency(error.message))
