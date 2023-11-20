import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { AbstractSettingsProvider } from '@app/file-storage/shared/services/abstract-settings-provider.service'
import { Injectable } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class NotificationSettingsProvider extends AbstractSettingsProvider {
  constructor(private readonly configService: ConfigService) {
    super()
  }

  async getSettings(appKey: string): Promise<IApplicationSettings> {
    return this.configService.get<IApplicationSettings>(`applications.${appKey}`)
  }
}
