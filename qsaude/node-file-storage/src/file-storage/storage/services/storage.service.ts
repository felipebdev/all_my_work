import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { AbstractSettingsProvider } from '@app/file-storage/shared/services/abstract-settings-provider.service'
import { AbstractStorageDriverService } from '@app/file-storage/storage/drivers/abstract-storage-driver.service'
import { IFile } from '@app/file-storage/storage/interfaces/models/file.interface'
import { AbstractStorageService } from '@app/file-storage/storage/services/abstract-storage.service'
import { Injectable } from '@nestjs/common'

@Injectable()
export class StorageService extends AbstractStorageService {
  constructor(
    private readonly settingsProvider: AbstractSettingsProvider,
    private readonly storageDriver: AbstractStorageDriverService
  ) {
    super()
  }

  async getSettings(appKey: string): Promise<IApplicationSettings> {
    return this.settingsProvider.getSettings(appKey)
  }

  async uploadFile(file: IFile, settings: IApplicationSettings): Promise<any> {
    return this.storageDriver.uploadFile(file, settings)
  }

  async downloadFile(fileKey: string, settings: IApplicationSettings): Promise<any> {
    return this.storageDriver.downloadFile(fileKey, settings)
  }
}
