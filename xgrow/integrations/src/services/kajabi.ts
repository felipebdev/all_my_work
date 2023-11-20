import axios from 'axios'
import { IGrantAccess, IRevokeAccess } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'

enum Webhook{
  ACTIVATION_URL = 'activation',
  DEACTIVATION_URL = 'deactivation'
}

interface GrantAccessBody {
  name: string
  email: string
  external_user_id: string
}

/**
 * @see https://help.kajabi.com/hc/en-us/articles/360037245374-How-to-Use-Webhooks-on-Kajabi
 */
export class Kajabi extends BaseService implements IGrantAccess, IRevokeAccess {
  protected validateSchema = {
    'header.app.action': value => (!!value && ['bindGrantAccess', 'bindRevokeAccess'].includes(value)),
    'header.app.planIds': value => value.length !== 0,
    'header.app.metadata.product_webhook': value => !!value,
    'payload.data.subscriber_name': value => !!value,
    'payload.data.subscriber_email': value => !!value
  }

  public async bindGrantAccess (payload: Payload): Promise<any> {
    return await this.postback(payload, Webhook.ACTIVATION_URL)
  }

  public async bindRevokeAccess (payload: Payload): Promise<any> {
    return await this.postback(payload, Webhook.DEACTIVATION_URL)
  }

  private async postback (payload: Payload, webhook: Webhook): Promise<any> {
    let {
      metadata: {
        product_webhook: productWebhook
      }
    } = payload.header.app

    const {
      subscriber_id: subscriberId,
      subscriber_name: subscriberName,
      subscriber_email: subscriberEmail
    } = payload.payload.data

    const body: GrantAccessBody = {
      name: subscriberName,
      email: subscriberEmail,
      external_user_id: String(subscriberId)
    }

    const splitedWebhook = productWebhook.split('?')
    if (!splitedWebhook.includes('send_offer_grant_email=true')) {
      productWebhook = `${productWebhook}?send_offer_grant_email=true`
    }

    return await axios.post(productWebhook, body)
  }
}
