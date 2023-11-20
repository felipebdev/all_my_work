import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'

export interface ISettingsProvider {
  getSettings(appKey: string): Promise<IApplicationSettings>
}
