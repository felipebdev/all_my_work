import { NotificationService } from '@app/file-storage/notification/services/notification.service'
import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { IApplicationsSettings } from '@app/file-storage/shared/interfaces/applications-settings.interface'
import { StorageController } from '@app/file-storage/storage/controllers/storage.controller'
import { PostFileNotificationsEnum } from '@app/file-storage/storage/enums/storage.notifications.enum'
import { IPostFileOptions } from '@app/file-storage/storage/interfaces/models/post-file-options.interface'
import { AbstractStorageService } from '@app/file-storage/storage/services/abstract-storage.service'
import { StorageService } from '@app/file-storage/storage/services/storage.service'
import { Test, TestingModule } from '@nestjs/testing'
import getStream from 'get-stream'
import { mock } from 'jest-mock-extended'
import { Readable } from 'stream'

describe('FileStorageController', () => {
  let file: Express.Multer.File
  let storageService: StorageService
  let notificationService: NotificationService
  let controller: StorageController

  beforeEach(async () => {
    jest.clearAllMocks()
    file = mock<Express.Multer.File>({
      filename: 'test',
      destination: 'test',
      mimetype: 'image/png',
      path: 'test',
      size: 1
    })
    storageService = mock<StorageService>()
    notificationService = mock<NotificationService>()
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
          provide: AbstractStorageService,
          useValue: storageService
        },
        {
          provide: NotificationService,
          useValue: notificationService
        }
      ],
      controllers: [StorageController]
    }).compile()

    controller = module.get<StorageController>(StorageController)
  })

  it('should be defined', () => {
    expect(controller).toBeDefined()
  })
  describe('handlePutFile', () => {
    it('should execute handle post file flow without options correctly', async () => {
      const mockedStorageSettings = mock<IApplicationSettings>()
      storageService.getSettings = jest.fn().mockResolvedValue(mockedStorageSettings)
      const result = await controller.handlePostFile('testAppKey', file)
      expect(storageService.getSettings).toHaveBeenCalledTimes(1)
      expect(storageService.getSettings).toHaveBeenCalledWith('testAppKey')
      expect(storageService.uploadFile).toHaveBeenCalledTimes(1)
      expect(storageService.uploadFile).toHaveBeenCalledWith(file, mockedStorageSettings)
      expect(result).toEqual({ fileKey: 'test' })
    })
    it('should execute handle post file flow without options parameters and throw a error if file storage service get settings throws', async () => {
      storageService.getSettings = jest.fn().mockRejectedValueOnce(new Error())
      await expect(controller.handlePostFile('testAppKey', file)).rejects.toThrow()
    })
    it('should execute handle post file flow without options parameters and throw a error if file storage service upload file throws', async () => {
      storageService.uploadFile = jest.fn().mockRejectedValueOnce(new Error())
      await expect(controller.handlePostFile('testAppKey', file)).rejects.toThrow()
    })
    it('should execute handle post file flow with options parameters correctly', async () => {
      const mockedStorageSettings = mock<IApplicationSettings>()
      const mockNotificationSettings = mock<IApplicationSettings>()
      storageService.getSettings = jest.fn().mockResolvedValue(mockedStorageSettings)
      notificationService.getSettings = jest.fn().mockResolvedValue(mockNotificationSettings)
      const options: IPostFileOptions = {
        notifications: [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]
      }
      file = mock<Express.Multer.File>({
        filename: 'test'
      })
      storageService.uploadFile = jest.fn().mockResolvedValueOnce(file)
      const handleFileNotificationsSpy = jest
        .spyOn(controller, 'handleFileNotifications')
        .mockResolvedValueOnce([{ [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]: 'fulfilled' }])
      const result = await controller.handlePostFile('testAppKey', file, options)
      expect(storageService.getSettings).toHaveBeenCalledTimes(1)
      expect(storageService.getSettings).toHaveBeenCalledWith('testAppKey')
      expect(storageService.uploadFile).toHaveBeenCalledTimes(1)
      expect(storageService.uploadFile).toHaveBeenCalledWith(file, mockedStorageSettings)
      expect(notificationService.getSettings).toHaveBeenCalledTimes(1)
      expect(notificationService.getSettings).toHaveBeenCalledWith('testAppKey')
      expect(handleFileNotificationsSpy).toHaveBeenCalledTimes(options.notifications.length)
      expect(handleFileNotificationsSpy).toHaveBeenCalledWith(
        file,
        [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS],
        mockNotificationSettings
      )
      expect(result).toEqual({
        fileKey: file.filename,
        notifications: [{ [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]: 'fulfilled' }]
      })
    })
    it('should execute handle post file flow with options parameters and return a notification with rejected result when handle file notifications throws', async () => {
      const mockedStorageSettings = mock<IApplicationsSettings>()
      const mockNotificationSettings = mock<IApplicationSettings>()
      storageService.getSettings = jest.fn().mockResolvedValue(mockedStorageSettings)
      notificationService.getSettings = jest.fn().mockResolvedValue(mockNotificationSettings)
      const options: IPostFileOptions = {
        notifications: [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]
      }
      file = mock<Express.Multer.File>({
        filename: 'test'
      })
      storageService.uploadFile = jest.fn().mockResolvedValueOnce(file)
      jest
        .spyOn(controller, 'handleFileNotifications')
        .mockResolvedValueOnce([{ [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]: 'rejected' }])
      const result = await controller.handlePostFile('testAppKey', file, options)
      expect(result).toEqual({
        fileKey: file.filename,
        notifications: [{ [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]: 'rejected' }]
      })
    })
  })
  describe('handleGetFile', () => {
    it('should execute handle get file correctly', async () => {
      const mockedStorageSettings = mock<IApplicationSettings>()
      storageService.getSettings = jest.fn().mockResolvedValue(mockedStorageSettings)
      storageService.downloadFile = jest.fn().mockResolvedValue({ Body: Readable.from('test', { encoding: 'utf8' }) })
      const stream = await controller.handleGetFile('testAppKey', 'test-file-key')
      const resultedStream = stream.getStream()
      const result = await getStream(resultedStream)
      expect(storageService.getSettings).toHaveBeenCalledTimes(1)
      expect(storageService.getSettings).toHaveBeenCalledWith('testAppKey')
      expect(storageService.downloadFile).toHaveBeenCalledTimes(1)
      expect(storageService.downloadFile).toHaveBeenCalledWith('test-file-key', mockedStorageSettings)
      expect(result).toEqual('test')
    })
    it('should throw a error if file storage service get settings throws', async () => {
      storageService.getSettings = jest.fn().mockRejectedValueOnce(new Error())
      await expect(controller.handleGetFile('testAppKey', 'test-file-key')).rejects.toThrow()
    })
    it('should throw a error if file storage service download file throws', async () => {
      storageService.downloadFile = jest.fn().mockRejectedValueOnce(new Error())
      await expect(controller.handleGetFile('testAppKey', 'test-file-key')).rejects.toThrow()
    })
  })
  describe('handleFileNotifications', () => {
    it('should execute handle file notification flow with correct values correctly', async () => {
      const mockedStorageSettings = mock<IApplicationSettings>({
        notification: {
          [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]: 'dummy_importCsvTransactions'
        }
      })
      storageService.getSettings = jest.fn().mockResolvedValue(mockedStorageSettings)
      file = mock<Express.Multer.File>({
        filename: 'test'
      })
      const options: IPostFileOptions = {
        notifications: [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]
      }
      const result = await controller.handleFileNotifications(file, options.notifications, mockedStorageSettings)
      expect(result).toEqual([{ [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]: 'fulfilled' }])
    })
    it('should execute handle file notification flow with correct values correctly and return a notification with status rejected', async () => {
      const mockedStorageSettings = mock<IApplicationSettings>({
        notification: {
          [PostFileNotificationsEnum.IMPORT_CSV_TRANSACTIONS]: 'dummy_importCsvTransactions'
        }
      })
      storageService.getSettings = jest.fn().mockResolvedValue(mockedStorageSettings)
      file = mock<Express.Multer.File>({
        filename: 'test'
      })
      const options = {
        notifications: ['wrong_notification']
      }
      const result = await controller.handleFileNotifications(file, options.notifications, mockedStorageSettings)
      expect(result).toEqual([{ wrong_notification: 'rejected' }])
    })
  })
})
