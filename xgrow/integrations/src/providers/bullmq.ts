import { Queue, Worker, Job } from 'bullmq'
import env from '../config/env'
import IConsumable from '../contracts/consumable'
import { LogFactory } from '../database/mongodb/factories'
import { JobResponse } from '../job'
import WinstonLog from './winston'
import ILogable from '@app/contracts/logable'
import apm from 'elastic-apm-node'
import IORedis from 'ioredis'
import { capitalize } from '../utils/helper'

class BullMq implements IConsumable {
  public readonly queue: Queue
  private readonly connection: IORedis
  private readonly logFactory: LogFactory = new LogFactory()
  private readonly logger: ILogable = WinstonLog.getInstance()
  constructor () {
    this.connection = new IORedis(env.redis)
    this.queue = new Queue(env.queue.name, { connection: this.connection })
    /* istanbul ignore next */
    this.queue.on('error', (error) => {
      this.logger.emergency('REDIS ERROR', error)
    })
  }

  consume (
    callable: CallableFunction,
    resolveFn: CallableFunction,
    rejectFn: CallableFunction
  ): void {
    const worker = new Worker(env.queue.name, async (job: Job) => {
      const { id, data } = job
      const integrationName = capitalize(data.header.app.integration.type)
      const apmTransaction = apm.startTransaction(`${integrationName}`, 'job')
      const jobResponse: JobResponse = {
        payload: data,
        jobId: String(id),
        isRetry: job.attemptsMade > 1
      }

      let response

      try {
        response = await callable(data)
        jobResponse.response = this.logFactory.handleResponse(response)
        apmTransaction.result = 'success'
        apmTransaction.end()
        return jobResponse
      } catch (error) {
        response = error
        jobResponse.error = error.message || 'Error not specified'
        jobResponse.response = this.logFactory.handleResponse(error.response)
        if (error.message === 'Payload is invalid') {
          job.opts.attempts = 1
          // await job.moveToFailed(new Error(error.message), this.queue.token, true)
        }
        apmTransaction.result = 'error'
        apm.captureError(error)
        apmTransaction.end()
        // eslint-disable-next-line @typescript-eslint/no-throw-literal
        throw jobResponse
      }
    }, { connection: this.connection })

    worker.on('completed', async({ returnvalue }): Promise<void> => {
      await resolveFn(returnvalue)
    })

    worker.on('failed', async(job: Job, error) => {
      await rejectFn(error)
    })
  }
}

export default abstract class BullMqProvider {
  static instance: BullMq
  static getInstance (): BullMq {
    if (!BullMqProvider.instance) {
      BullMqProvider.instance = new BullMq()
    }

    return BullMqProvider.instance
  }
}
