import { AbstractNotificationService } from '@app/file-storage/notification/services/abstract-notification.service'
import { NotificationService } from '@app/file-storage/notification/services/notification.service'
import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { HandlePostFileDto } from '@app/file-storage/storage/dtos/handle-post-file.dto'
import { IHandlerGetFile } from '@app/file-storage/storage/interfaces/handlers/handle-get-file.interface'
import { IHandlerPostFile } from '@app/file-storage/storage/interfaces/handlers/handle-post-file.interface'
import { IPostFileOptions } from '@app/file-storage/storage/interfaces/models/post-file-options.interface'
import { AbstractStorageService } from '@app/file-storage/storage/services/abstract-storage.service'
import {
  Body,
  Controller,
  forwardRef,
  Get,
  Inject,
  Param,
  Post,
  StreamableFile,
  UploadedFile,
  UseGuards,
  UseInterceptors
} from '@nestjs/common'
import { AuthGuard } from '@nestjs/passport'
import { FileInterceptor } from '@nestjs/platform-express'
import { ApiBody, ApiConsumes, ApiTags } from '@nestjs/swagger'
import { camelCase } from 'change-case'
import * as mime from 'mime-types'

@ApiTags('Storage Resource')
@Controller()
export class StorageController implements IHandlerPostFile, IHandlerGetFile {
  constructor(
    private readonly storageService: AbstractStorageService,
    @Inject(forwardRef(() => NotificationService)) private readonly notificationService: AbstractNotificationService
  ) {}

  async handleFileNotifications(
    file: Express.Multer.File,
    notifications: string[],
    notificationSettings: IApplicationSettings
  ): Promise<unknown[]> {
    const promisesResults = await Promise.allSettled(
      notifications
        .map(async (notification) => {
          const topic = notificationSettings.notification[notification]
          if (typeof topic === 'string') {
            return this.notificationService.notify(topic, file)
          }
          throw new Error(`Notification ${notification} is not defined`)
        })
        .filter((item) => item !== undefined)
    )
    return promisesResults.map((promise, index) => {
      return {
        [notifications[index]]: promise.status
      }
    })
  }

  @UseGuards(AuthGuard('jwt'))
  @Post(':appKey')
  @UseInterceptors(FileInterceptor('file'))
  @ApiConsumes('multipart/form-data')
  @ApiBody({ type: HandlePostFileDto, description: 'File to be storage' })
  async handlePostFile(
    @Param('appKey') appKey: string,
    @UploadedFile() file: Express.Multer.File,
    @Body('options') options?: IPostFileOptions
  ): Promise<any> {
    appKey = camelCase(appKey)
    const storageSettings = await this.storageService.getSettings(appKey)
    await this.storageService.uploadFile(file, storageSettings)
    const result = { fileKey: file.filename }
    if (options && options.notifications.length > 0) {
      const notificationSettings = await this.notificationService.getSettings(appKey)
      const notifications = await this.handleFileNotifications(file, options.notifications, notificationSettings)
      if (notifications.length > 0) {
        result['notifications'] = notifications
      }
    }
    return result
  }

  @UseGuards(AuthGuard('jwt'))
  @Get(':appKey/:fileKey')
  async handleGetFile(@Param('appKey') appKey: string, @Param('fileKey') fileKey: string): Promise<StreamableFile> {
    appKey = camelCase(appKey)
    const storageSettings = await this.storageService.getSettings(appKey)
    const result = await this.storageService.downloadFile(fileKey, storageSettings)
    return new StreamableFile(result.Body, {
      type: result.ContentType,
      length: result.ContentLength,
      disposition: `attachment; filename=${fileKey}.${mime.extension(result.ContentType)}`
    })
  }
}
