import { Body, Controller, Post } from '@nestjs/common'
import { SqsProducer } from '../decorators'

@Controller('sqs')
export class SqsController {
  @Post()
  @SqsProducer('sample-queue')
  public async produceMessage(
    @Body() body
  ) {
    return body
  }
}
