import { NotImplementedException } from '@nestjs/common'
import { Test, TestingModule } from '@nestjs/testing'
import { AbstractSqsPoller } from './abstract'
import { SqsPoller } from './sqs.poller'

describe('SqsPoller', () => {
  let sut: SqsPoller
  let sqsMessage: AWS.SQS.Message

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
            provide: AbstractSqsPoller,
            useClass: SqsPoller
        }
      ]
    }).compile()

    sut = module.get<AbstractSqsPoller>(AbstractSqsPoller)
    sqsMessage = {
      Body:'anytext',
      MessageId: 'anyid'
    }
  })

  it('should be defined', () => {
    expect(sut).toBeDefined()
  })

  describe('handleMessage()', () => {
    it('should throw not implemented exception', async ()=>{
      await expect(sut.handleMessage(sqsMessage)).rejects.toThrow(
            new NotImplementedException()
        )
    })
  })

  describe('onProcessingError()', () => {
    it('should throw not implemented exception', async ()=>{
        await expect(sut.onProcessingError(new Error('any'), sqsMessage)).rejects.toThrow(
            new NotImplementedException()
        )
    })
  })

  describe('onError()', () => {
    it('should throw not implemented exception', async ()=>{
      await expect(sut.onError(new Error('any'), sqsMessage)).rejects.toThrow(
            new NotImplementedException()
        )
    })
  })
})
