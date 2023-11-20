import { AbstractNotificationClient } from '@app/file-storage/notification/clients/abstract-notification-client.service'
import { INotifier } from '@app/file-storage/notification/interfaces/services/notifier.interface'
import { IAwsSettings } from '@app/file-storage/shared/interfaces/aws-settings.interface'
import { PublishCommand, PublishCommandInput, PublishCommandOutput, SNSClient } from '@aws-sdk/client-sns'
import { Injectable } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class SnsNotificationClient
  extends AbstractNotificationClient
  implements INotifier<Record<string, any>, PublishCommandOutput>
{
  constructor(private readonly client: SNSClient, private readonly configService: ConfigService) {
    super()
  }

  async notify<Data, PublishCommandOutput>(topic: string, data: Data): Promise<PublishCommandOutput> {
    const { region, accountId } = this.configService.get<IAwsSettings>('main.aws')
    const topicArn = `arn:aws:sns:${region}:${accountId}:${topic}`
    const inputCommand: PublishCommandInput = {
      TopicArn: topicArn,
      Message: JSON.stringify(data)
    }
    console.debug(`Sending notification to topic ${topicArn} with data: ${JSON.stringify(inputCommand)}`)
    const command = new PublishCommand(inputCommand)
    return this.client.send(command) as unknown as PublishCommandOutput
  }
}
