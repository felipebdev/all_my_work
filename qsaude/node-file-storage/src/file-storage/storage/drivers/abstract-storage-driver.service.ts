import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { IFile } from '@app/file-storage/storage/interfaces/models/file.interface'
import { IDownloadFile } from '@app/file-storage/storage/interfaces/services/download-file.interface'
import { IUploadFile } from '@app/file-storage/storage/interfaces/services/upload-file.interface'

export abstract class AbstractStorageDriverService implements IUploadFile, IDownloadFile {
  abstract uploadFile(file: IFile, settings: IApplicationSettings): Promise<unknown>
  abstract downloadFile(fileKey: string, settings: IApplicationSettings): Promise<unknown>
}
