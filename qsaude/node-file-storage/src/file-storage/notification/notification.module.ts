import { AbstractNotificationClient } from '@app/file-storage/notification/clients/abstract-notification-client.service'
import { SnsNotificationClient } from '@app/file-storage/notification/clients/sns-notification-client.service'
import { NotificationSettingsProvider } from '@app/file-storage/notification/services/notification-settings.provider'
import { NotificationService } from '@app/file-storage/notification/services/notification.service'
import { IMainSettings } from '@app/file-storage/shared/interfaces/main-settings.interface'
import { AbstractSettingsProvider } from '@app/file-storage/shared/services/abstract-settings-provider.service'
import { SNSClient, SNSClientConfig } from '@aws-sdk/client-sns'
import { Module } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Module({
  providers: [
    {
      provide: SNSClient,
      inject: [ConfigService],
      useFactory: async (configService: ConfigService) => {
        const configs = configService.get<IMainSettings>('main')
        const awsConfig: SNSClientConfig = {
          region: configs.aws.region
        }
        return new SNSClient(awsConfig)
      }
    },
    {
      provide: AbstractSettingsProvider,
      useClass: NotificationSettingsProvider
    },
    {
      provide: AbstractNotificationClient,
      useClass: SnsNotificationClient
    },
    NotificationService
  ],
  exports: [NotificationService]
})
export class NotificationModule {}
