import { createMock } from '@golevelup/nestjs-testing'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { SqsController } from '@app/sqs/controllers'
import { SqsService } from '@nestjs-packages/sqs'

describe('SqsController', () => {
  let sut: SqsController
  let message;

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'test')
  })

  const mockSqsService = createMock<SqsService>({
    send: jest.fn(() => ({}))
  })

  beforeEach(async () => {
    jest.clearAllMocks()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      controllers: [SqsController],
      providers:[SqsService]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .overrideProvider(SqsService)
      .useValue(mockSqsService)
      .compile()
    sut = module.get<SqsController>(SqsController)
    message = {
      any: 'any'
    }
  })

  it('should be defined', () => {
    expect(sut).toBeDefined()
  })
  describe('produceMessage()', () => {
    it('should call SqsProducer() decorator correctly', async () => {
        await sut.produceMessage(message)
        expect(mockSqsService.send).toBeCalledTimes(1)
        expect(mockSqsService.send).toBeCalledWith('sample-queue',{
          body:message,
          id:'1234'
        })
    })
    it('should throw an error when SqsService send() fails', async () => {
      jest.spyOn(mockSqsService, 'send').mockImplementation(()=>{
        throw new Error()
      })
      await expect(sut.produceMessage(message)).rejects.toThrow(
        new Error('Somenthing went wrong.')
      )
      expect(mockSqsService.send).toBeCalledTimes(1)
      expect(mockSqsService.send).toBeCalledWith('sample-queue',{
        body:message,
        id:'1234'
      })
    })

  })
})
