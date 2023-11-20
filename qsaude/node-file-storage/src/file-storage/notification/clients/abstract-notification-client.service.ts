import { INotifier } from '@app/file-storage/notification/interfaces/services/notifier.interface'
import { Injectable } from '@nestjs/common'

@Injectable()
export abstract class AbstractNotificationClient implements INotifier<unknown, unknown> {
  protected serviceArn: string
  abstract notify(topic: string, data: unknown): Promise<unknown>
}
