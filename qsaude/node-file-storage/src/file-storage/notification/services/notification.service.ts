import { AbstractNotificationClient } from '@app/file-storage/notification/clients/abstract-notification-client.service'
import { INotifier } from '@app/file-storage/notification/interfaces/services/notifier.interface'
import { AbstractNotificationService } from '@app/file-storage/notification/services/abstract-notification.service'
import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { AbstractSettingsProvider } from '@app/file-storage/shared/services/abstract-settings-provider.service'
import { Injectable } from '@nestjs/common'

@Injectable()
export class NotificationService extends AbstractNotificationService implements INotifier<unknown, unknown> {
  constructor(
    private readonly settingsProvider: AbstractSettingsProvider,
    private readonly notificationClient: AbstractNotificationClient
  ) {
    super()
  }

  async notify(topic: string, data: unknown): Promise<unknown> {
    return this.notificationClient.notify(topic, data)
  }

  async getSettings(appKey: string): Promise<IApplicationSettings> {
    return this.settingsProvider.getSettings(appKey)
  }
}
