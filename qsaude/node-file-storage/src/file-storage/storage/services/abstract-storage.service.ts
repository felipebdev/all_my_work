import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { ISettingsProvider } from '@app/file-storage/shared/interfaces/settings-provider.interface'
import { IFile } from '@app/file-storage/storage/interfaces/models/file.interface'
import { IDownloadFile } from '@app/file-storage/storage/interfaces/services/download-file.interface'
import { IUploadFile } from '@app/file-storage/storage/interfaces/services/upload-file.interface'

export abstract class AbstractStorageService implements ISettingsProvider, IUploadFile, IDownloadFile {
  abstract getSettings(appKey: string): Promise<IApplicationSettings>
  abstract uploadFile(file: IFile, settings: IApplicationSettings): Promise<any>
  abstract downloadFile(fileKey: string, settings: IApplicationSettings): Promise<any>
}
