import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { AbstractSettingsProvider } from '@app/file-storage/shared/services/abstract-settings-provider.service'
import { AbstractStorageDriverService } from '@app/file-storage/storage/drivers/abstract-storage-driver.service'
import { StorageService } from '@app/file-storage/storage/services/storage.service'
import { Test } from '@nestjs/testing'
import { mock } from 'jest-mock-extended'

describe('FileStorageService', () => {
  let file: Express.Multer.File
  let storageDriver: AbstractStorageDriverService
  let settingsProvider: AbstractSettingsProvider
  let storageService: StorageService
  beforeEach(async () => {
    jest.clearAllMocks()
    file = mock<Express.Multer.File>()
    settingsProvider = mock<AbstractSettingsProvider>()
    storageDriver = mock<AbstractStorageDriverService>()
    const moduleRef = await Test.createTestingModule({
      imports: [],
      controllers: [],
      providers: [
        {
          provide: AbstractSettingsProvider,
          useValue: settingsProvider
        },
        {
          provide: AbstractStorageDriverService,
          useValue: storageDriver
        },
        StorageService
      ]
    }).compile()

    storageService = moduleRef.get<StorageService>(StorageService)
  })

  it('should be defined', () => {
    expect(storageService).toBeDefined()
  })
  describe('uploadFile', () => {
    it('should call storage driver uploadFile correctly', async () => {
      storageDriver.uploadFile = jest.fn().mockResolvedValueOnce('test')
      const mockedSettings = mock<IApplicationSettings>()
      const result = await storageService.uploadFile(file, mockedSettings)
      expect(storageDriver.uploadFile).toBeCalledTimes(1)
      expect(storageDriver.uploadFile).toBeCalledWith(file, mockedSettings)
      expect(result).toEqual('test')
    })
    it('should throw a error if storage driver uploadFile throws', async () => {
      storageDriver.uploadFile = jest.fn().mockRejectedValue(new Error())
      const mockedSettings = mock<IApplicationSettings>()
      await expect(storageService.uploadFile(file, mockedSettings)).rejects.toThrow()
    })
  })
  describe('downloadFile', () => {
    it('should call storage driver download file with correct parameters correctly', async () => {
      storageDriver.downloadFile = jest.fn().mockResolvedValue('test')
      const mockedSettings = mock<IApplicationSettings>()
      const result = await storageService.downloadFile('test-file-key', mockedSettings)
      expect(storageDriver.downloadFile).toBeCalledTimes(1)
      expect(storageDriver.downloadFile).toBeCalledWith('test-file-key', mockedSettings)
      expect(result).toEqual('test')
    })
    it('should throw a error if storage driver download file throws', async () => {
      storageDriver.downloadFile = jest.fn().mockRejectedValue(new Error())
      const mockedSettings = mock<IApplicationSettings>()
      await expect(storageService.downloadFile('test-file-key', mockedSettings)).rejects.toThrow()
    })
  })
  describe('getSettings', () => {
    it('should call storage driver downloadFile with correct parameters correctly', async () => {
      const mockedSettings = mock<IApplicationSettings>()
      settingsProvider.getSettings = jest.fn().mockResolvedValue(mockedSettings)
      const result = await storageService.getSettings('test-file-key')
      expect(settingsProvider.getSettings).toBeCalledTimes(1)
      expect(settingsProvider.getSettings).toBeCalledWith('test-file-key')
      expect(result).toEqual(mockedSettings)
    })
    it('should throw a error if storage driver throws', async () => {
      settingsProvider.getSettings = jest.fn().mockRejectedValueOnce(new Error())
      await expect(storageService.getSettings('test-file-key')).rejects.toThrow()
    })
  })
})
