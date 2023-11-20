import axios from 'axios'
import { IGrantAccess, IRevokeAccess } from '../contracts/actions'
import { Payload } from '../job'
import { onlyNumbers } from '../utils/helper'
import { BaseService } from './base'

enum Status {
  APPROVED = 'aprovado',
  CANCELLED = 'cancelado',
  CONTESTED = 'disputa'
}

interface GrantAccessBody {
  token: string
  codigo: string
  produto_id: string
  produto_nome?: string
  valor?: number
  cliente_nome?: string
  cliente_celular?: string
  cliente_email: string
  cliente_doc?: string
  status: string
  recorrencia_id?: string
  recorrencia_status?: string
}

/**
 * @see https://docs.cademi.com.br/entregas/custom
 */
export class Cademi extends BaseService implements IGrantAccess, IRevokeAccess {
  protected validateSchema = {
    'header.app.action': value => (!!value && ['bindGrantAccess', 'bindRevokeAccess'].includes(value)),
    'header.app.planIds': value => value.length !== 0,
    'header.app.integration.api_key': value => !!value,
    'header.app.integration.api_webhook': value => !!value,
    'payload.data.subscriber_name': value => !!value,
    'payload.data.subscriber_document_number': value => !!value,
    'payload.data.subscriber_email': value => !!value,
    'payload.data.payment_order_code': value => !!value,
    'payload.data.payment_plans': value => value.length !== 0
  }

  public async bindGrantAccess (payload: Payload): Promise<void> {
    await this.postback(payload, Status.APPROVED)
  }

  public async bindRevokeAccess (payload: Payload): Promise<void> {
    await this.postback(payload, Status.CANCELLED)
  }

  private async postback (payload: Payload, status: Status): Promise<void> {
    const {
      planIds,
      integration: {
        api_key: apiKey,
        api_webhook: apiWebhook
      }
    } = payload.header.app

    const {
      subscriber_name: subscriberName,
      subscriber_document_number: subscriberDocument,
      subscriber_email: subscriberEmail,
      subscriber_phone: subscriberPhone = null,
      payment_order_code: paymentOrderCode,
      payment_plans: paymentPlans
    } = payload.payload.data

    for (const plan of paymentPlans) {
      if (planIds.includes(Number(plan.id))) {
        const body: GrantAccessBody = {
          token: apiKey,
          codigo: paymentOrderCode,
          produto_id: String(plan.id),
          cliente_nome: subscriberName,
          cliente_celular: onlyNumbers(subscriberPhone),
          cliente_email: subscriberEmail,
          cliente_doc: onlyNumbers(subscriberDocument),
          status
        }
        await axios.post(apiWebhook, body)
      }
    }
  }
}
