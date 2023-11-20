import { IApplicationsSettings } from '@app/file-storage/shared/interfaces/applications-settings.interface'
import { StorageSettingsProvider } from '@app/file-storage/storage/services/storage-settings.provider'
import { ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'
import { mock } from 'jest-mock-extended'

describe('StorageSettingsProvider', () => {
  let storageSettingsProvider: StorageSettingsProvider
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
        StorageSettingsProvider
      ]
    }).compile()

    storageSettingsProvider = moduleRef.get<StorageSettingsProvider>(StorageSettingsProvider)
  })

  it('should be defined', () => {
    expect(storageSettingsProvider).toBeDefined()
  })
  describe('getSettings', () => {
    it('should get storage options', async () => {
      const mockedSettings = mock<IApplicationsSettings>()
      configService.get.mockReturnValueOnce(mockedSettings)
      const settings = await storageSettingsProvider.getSettings('testAppKey')
      expect(settings).toEqual(mockedSettings)
    })
    it('should return a throw if config service throws', async () => {
      configService.get.mockImplementationOnce(() => {
        throw new Error()
      })
      await expect(storageSettingsProvider.getSettings('testAppKey')).rejects.toThrow()
    })
  })
})
