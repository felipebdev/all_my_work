import { Payload } from '../../job'

export interface IPushNotifications {
  bindPushNotification: (payload: Payload) => Promise<any>
}
