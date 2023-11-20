import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'

export interface IApplicationsSettings {
  [key: string]: IApplicationSettings
}
