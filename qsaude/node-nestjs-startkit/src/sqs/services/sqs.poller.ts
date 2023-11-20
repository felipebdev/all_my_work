import { SqsConsumerEvent, SqsConsumerEventHandler, SqsMessageHandler, SqsProcess } from '@nestjs-packages/sqs'
import { NotImplementedException } from '@nestjs/common'
import { AbstractSqsPoller } from '@app/sqs/services/abstract'

@SqsProcess('sample-queue')
export class SqsPoller implements AbstractSqsPoller {
  @SqsMessageHandler(false)
  public async handleMessage(message: AWS.SQS.Message): Promise<any> {
    console.log('MESSAGE: ', message)
    throw new NotImplementedException()
  }

  @SqsConsumerEventHandler(SqsConsumerEvent.PROCESSING_ERROR)
  public async onProcessingError(error: Error, message: AWS.SQS.Message): Promise<any> {
    console.log('PROCESSING_ERROR: ', error)
    throw new NotImplementedException()
  }

  @SqsConsumerEventHandler(SqsConsumerEvent.ERROR)
  public async onError(error: Error, message: AWS.SQS.Message): Promise<any> {
    console.log('ERROR: ', error)
    throw new NotImplementedException()
  }
}
