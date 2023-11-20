import { AbstractNotificationClient } from '@app/file-storage/notification/clients/abstract-notification-client.service'
import { INotificationResult } from '@app/file-storage/notification/interfaces/models/notification-result.interface'
import { INotifier } from '@app/file-storage/notification/interfaces/services/notifier.interface'
import { NotificationService } from '@app/file-storage/notification/services/notification.service'
import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { AbstractSettingsProvider } from '@app/file-storage/shared/services/abstract-settings-provider.service'
import { Test, TestingModule } from '@nestjs/testing'

class NotificationClientStub extends AbstractNotificationClient implements INotifier<any, INotificationResult> {
  async notify(topic: string, data: any): Promise<INotificationResult> {
    return { success: topic === 'dummy-topic' && JSON.stringify(data) === JSON.stringify({ data: 'dummy-data' }) }
  }
}

class SettingsProviderStub extends AbstractSettingsProvider {
  async getSettings(appKey: string): Promise<IApplicationSettings> {
    const result: IApplicationSettings = {
      aws: {
        s3: {
          bucket: appKey
        }
      },
      storage: {
        path: appKey
      },
      notification: {}
    }
    return result
  }
}

describe('NotificationService', () => {
  let notificationService: NotificationService
  let notificationClient: AbstractNotificationClient
  let settingsProvider: AbstractSettingsProvider
  beforeEach(async () => {
    notificationClient = new NotificationClientStub()
    settingsProvider = new SettingsProviderStub()
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
          provide: AbstractSettingsProvider,
          useValue: settingsProvider
        },
        {
          provide: AbstractNotificationClient,
          useValue: notificationClient
        },
        NotificationService
      ]
    }).compile()

    notificationService = module.get<NotificationService>(NotificationService)
  })

  it('should be defined', () => {
    expect(notificationService).toBeDefined()
  })
  describe('notify', () => {
    it('should call notification client notify correctly', async () => {
      const data = { data: 'dummy-data' }
      const topic = 'dummy-topic'
      const mockResult: INotificationResult = {
        success: true
      }
      const notificationClientSpy = jest.spyOn(notificationClient, 'notify')
      const result = await notificationService.notify(topic, data)
      expect(notificationClientSpy).toHaveBeenCalledTimes(1)
      expect(notificationClientSpy).toHaveBeenCalledWith(topic, data)
      expect(result).toEqual(mockResult)
    })
    it('should throw a error if notification client notify throws', async () => {
      const data = { data: 'dummy-data' }
      const topic = 'dummy-topic'
      jest.spyOn(notificationClient, 'notify').mockRejectedValueOnce(new Error())
      await expect(notificationService.notify(topic, data)).rejects.toThrow()
    })
  })
  describe('getSettings', () => {
    it('should call settings provider with correct parameters correctly', async () => {
      const settingsProviderSpy = jest.spyOn(settingsProvider, 'getSettings')
      const result = await notificationService.getSettings('testing-app-key')
      expect(settingsProviderSpy).toBeCalledTimes(1)
      expect(settingsProviderSpy).toBeCalledWith('testing-app-key')
      expect(result).toEqual(await settingsProvider.getSettings('testing-app-key'))
    })
    it('should throw a error if settings provider throws', async () => {
      jest.spyOn(settingsProvider, 'getSettings').mockRejectedValueOnce(new Error())
      await expect(notificationService.getSettings('testing-app-key')).rejects.toThrow()
    })
  })
})
