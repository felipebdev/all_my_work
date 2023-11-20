import { SqsQueueOptions } from '@nestjs-packages/sqs'
import { SqsAsyncConfig } from '@nestjs-packages/sqs/dist/sqs.interfaces'

export interface ISqsModuleConfig {
  config: SqsAsyncConfig
  queues: SqsQueueOptions
}
