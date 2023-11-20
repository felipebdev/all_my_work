import { SnsNotificationClient } from '@app/file-storage/notification/clients/sns-notification-client.service'
import { PublishCommand, PublishCommandOutput, SNSClient } from '@aws-sdk/client-sns'
import { ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'
import { mock } from 'jest-mock-extended'

jest.mock('@aws-sdk/client-sns', () => {
  const original = jest.requireActual('@aws-sdk/client-sns')
  return {
    ...original,
    PublishCommand: jest.fn(),
    PublishCommandOutput: jest.fn()
  }
})

describe('SnsNotification-clientService', () => {
  let snsNotificationClient: SnsNotificationClient
  const publishCommandOutput = mock<PublishCommandOutput>()
  const configService = mock<ConfigService>()
  const snsClient = {
    send: jest.fn()
  } as unknown as SNSClient
  beforeEach(async () => {
    jest.clearAllMocks()
    const moduleRef = await Test.createTestingModule({
      imports: [],
      controllers: [],
      providers: [
        {
          provide: ConfigService,
          useValue: configService
        },
        {
          provide: SNSClient,
          useValue: snsClient
        },
        SnsNotificationClient
      ]
    }).compile()

    snsNotificationClient = moduleRef.get<SnsNotificationClient>(SnsNotificationClient)
  })

  it('should be defined', () => {
    expect(snsNotificationClient).toBeDefined()
  })

  describe('notify', () => {
    it('should execute a notification notify flow', async () => {
      configService.get.mockReturnValue({
        region: 'region',
        accountId: 'accountId'
      })
      snsClient.send = jest.fn().mockImplementationOnce(async (): Promise<PublishCommandOutput> => publishCommandOutput)
      const payload = { data: 'message' }
      const result = await snsNotificationClient.notify('topic', payload)
      expect(PublishCommand).toHaveBeenCalledTimes(1)
      expect(PublishCommand).toHaveBeenCalledWith({
        TopicArn: 'arn:aws:sns:region:accountId:topic',
        Message: JSON.stringify(payload)
      })
      expect(snsClient.send).toHaveBeenCalledTimes(1)
      expect(snsClient.send).toHaveBeenCalledWith(expect.any(PublishCommand))
      expect(result).toBe(publishCommandOutput)
    })
    it('should throw a error if client throws', () => {
      snsClient.send = jest.fn().mockImplementationOnce(async () => {
        throw new Error()
      })
      const payload = { data: 'testing' }
      expect(snsNotificationClient.notify('topic', payload)).rejects.toThrow()
      expect(PublishCommand).toHaveBeenCalledTimes(1)
      expect(PublishCommand).toHaveBeenCalledWith({
        TopicArn: 'arn:aws:sns:region:accountId:topic',
        Message: JSON.stringify(payload)
      })
      expect(snsClient.send).toHaveBeenCalledTimes(1)
      expect(snsClient.send).toHaveBeenCalledWith(expect.any(PublishCommand))
    })
  })
})
