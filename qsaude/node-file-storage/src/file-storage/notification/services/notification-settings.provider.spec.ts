import { NotificationSettingsProvider } from '@app/file-storage/notification/services/notification-settings.provider'
import { IApplicationsSettings } from '@app/file-storage/shared/interfaces/applications-settings.interface'
import { ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'
import { mock } from 'jest-mock-extended'

describe('NotificationSettingsProvider', () => {
  let notificationSettingsProvider: NotificationSettingsProvider
  const configService = mock<ConfigService>()
  beforeEach(async () => {
    const moduleRef = await Test.createTestingModule({
      imports: [],
      controllers: [],
      providers: [
        {
          provide: ConfigService,
          useValue: configService
        },
        NotificationSettingsProvider
      ]
    }).compile()

    notificationSettingsProvider = moduleRef.get<NotificationSettingsProvider>(NotificationSettingsProvider)
  })

  it('should be defined', () => {
    expect(notificationSettingsProvider).toBeDefined()
  })
  describe('getSettings', () => {
    it('should get notification settings', async () => {
      const mockedSettings = mock<IApplicationsSettings>()
      configService.get.mockReturnValueOnce(mockedSettings)
      const settings = await notificationSettingsProvider.getSettings('testAppKey')
      expect(settings).toEqual(mockedSettings)
    })
    it('should return a throw if config service throws', async () => {
      configService.get.mockImplementationOnce(() => {
        throw new Error()
      })
      await expect(notificationSettingsProvider.getSettings('testAppKey')).rejects.toThrow()
    })
  })
})
