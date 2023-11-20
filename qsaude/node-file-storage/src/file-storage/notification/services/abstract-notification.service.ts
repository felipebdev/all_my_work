import { INotifier } from '@app/file-storage/notification/interfaces/services/notifier.interface'
import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { ISettingsProvider } from '@app/file-storage/shared/interfaces/settings-provider.interface'
import { Injectable } from '@nestjs/common'

@Injectable()
export abstract class AbstractNotificationService implements ISettingsProvider, INotifier<unknown, unknown> {
  abstract getSettings(appKey: string): Promise<IApplicationSettings>
  abstract notify(topic: string, data: unknown): Promise<unknown>
}
