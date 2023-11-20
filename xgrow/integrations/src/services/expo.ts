import { IPushNotifications } from '../contracts/actions/push-notification'
import { Payload } from '../job'
import { BaseService } from './base'
import { Expo as ExpoServer } from 'expo-server-sdk'

// https://docs.expo.io/push-notifications/sending-notifications/
export class Expo extends BaseService implements IPushNotifications {
  protected validateSchema = {}

  async bindPushNotification(payload: Payload): Promise<any> {
    const { expoTokens, messageTitle, messageBody, messageData } = payload.header.app.integration.metadata
    const expo = new ExpoServer()
    const messages = []

    const uniqueTokens = [...new Set(expoTokens)]

    for (const pushToken of uniqueTokens) {
      if (!ExpoServer.isExpoPushToken(pushToken)) {
        // eslint-disable-next-line @typescript-eslint/no-base-to-string
        console.error(`Push token ${pushToken} is not a valid Expo push token`)
        continue
      }

      messages.push({
        to: pushToken,
        title: messageTitle,
        body: messageBody,
        data: messageData,
      })
    }

    const chunks = expo.chunkPushNotifications(messages)
    const tickets = []

    for (const chunk of chunks) {
      try {
        const ticketChunk = await expo.sendPushNotificationsAsync(chunk)
        tickets.push(...ticketChunk)
      } catch (error) {
        console.error(error)
        throw new Error(error.mesage)
      }
    }
  }
}
