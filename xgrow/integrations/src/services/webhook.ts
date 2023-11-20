import axios from 'axios'
import { ITriggerWebhook } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'
import crypto from 'crypto'

interface Request {
  url: string
  headers: {
    Accept: string
    'Content-Type': string
    'X-Hub-Signature': string
  }
}
export class Webhook extends BaseService implements ITriggerWebhook {
  private static readonly HTTP_ACCEPT = 'application/json'
  private static readonly CONTENT_TYPE = 'application/json'
  protected validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindTriggerWebhook'].includes(value)),
    'header.app.integration.api_key': (value: string) => !!value,
    'header.app.integration.api_webhook': (value: string) => !!value
  }

  public async bindTriggerWebhook (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const body = payload.payload.data

    body.payment_date = body.payment_date ?? new Date()

    if (body.transaction_plans) {
      body.payment_plans = body.transaction_plans
    }

    return await axios.post(
      request.url,
      body,
      { headers: request.headers }
    )
  }

  private request (payload: Payload): Request {
    const { api_webhook: apiWebhook, api_key: apiKey } = payload.header.app.integration

    const request: Request = {
      url: apiWebhook,
      headers: {
        Accept: Webhook.HTTP_ACCEPT,
        'Content-Type': Webhook.CONTENT_TYPE,
        'X-Hub-Signature': this.assignRequest(apiKey, payload)
      }
    }

    return request
  }

  private assignRequest (apiKey: string, payload: Payload): string {
    const signature = crypto.createHmac('sha1', apiKey)
      .update(JSON.stringify(payload.payload.data)).digest('hex')

    return signature
  }
}
