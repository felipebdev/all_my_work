import { ISettingsProvider } from '@app/file-storage/shared/interfaces/settings-provider.interface'
import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'

export abstract class AbstractSettingsProvider implements ISettingsProvider {
  abstract getSettings(appKey: string): Promise<IApplicationSettings>
}
