import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { IFile } from '@app/file-storage/storage/interfaces/models/file.interface'

export interface IUploadFile {
  uploadFile(file: IFile, settings: IApplicationSettings): Promise<any>
}
