import { AxiosResponse } from 'axios'
import { ValidateJsAdapter } from './adapters/validatejs-adapter'
import ILogable from './contracts/logable'
import { IProcessable } from './contracts/processable'
import WinstonLog from './providers/winston'
import * as Integration from './services'
import { capitalize } from './utils/helper'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ApmSpan } from './decorators/apm-spam'

interface Action {
  id: number
  app_id: number
  platform_id: string
  platform_name?: string
  event: string
  action: string
  metadata?: any
  planIds: number[]
  integration: {
    id: number
    type: string
    api_key?: string
    api_account?: string
    api_webhook?: string
    api_secret?: string
    signature?: string
    metadata?: any
  }
}

export interface Payload {
  header: {
    date: string
    app: Action
    correlation_id?: string
  }
  payload: {
    data: any
  }
}

export interface JobResponse {
  payload: Payload
  jobId?: string
  error?: Error
  response?: AxiosResponse
  isRetry: boolean
}

export abstract class Job {
  private static readonly logger: ILogable = WinstonLog.getInstance()

  static async process (payload: Payload): Promise<any> {
    const integrationName = capitalize(payload.header.app.integration.type)
    Job.logger.info(`PAYLOAD RECEIVED FOR ${integrationName}`, payload)
    const integration: IProcessable = new Integration[integrationName](
      new ValidateJsAdapter(),
      payload
    )

    return Promise.resolve(await integration.process())
  }

  @ApmSpan('logging successful job response')
  static async resolve (response: JobResponse): Promise<void> {
    Job.logger.logJobResponse('INFO', 'JOB PROCESSED', response)
  }

  @ApmSpan('logging failed job response')
  static async reject (response: JobResponse): Promise<void> {
    Job.logger.logJobResponse('ERROR', 'JOB FAILED', response)
  }
}
