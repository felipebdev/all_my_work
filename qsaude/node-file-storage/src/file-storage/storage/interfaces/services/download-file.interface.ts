import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'

export interface IDownloadFile {
  downloadFile(fileKey: string, settings: IApplicationSettings): Promise<any>
}
