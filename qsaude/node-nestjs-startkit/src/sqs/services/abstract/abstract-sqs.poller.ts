import { ISqsPoller } from '@app/sqs/interfaces/sqs.poller.interface'

export abstract class AbstractSqsPoller implements ISqsPoller {
  abstract handleMessage(message: AWS.SQS.Message)
  abstract onProcessingError(error: Error, message: AWS.SQS.Message)
  abstract onError(error: Error, message: AWS.SQS.Message)
}
