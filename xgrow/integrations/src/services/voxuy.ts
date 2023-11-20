import axios from 'axios'
import { ITriggerWebhook } from '../contracts/actions'
import { Payload } from '../job'
import { onlyNumbers } from '../utils/helper'
import { BaseService } from './base'

enum PaymentType {
  GRATUITO = 0,
  BOLETO = 1,
  CREDIT_CARD = 2,
  PAYPAL = 3,
  BOLETO_PARCELADO = 4,
  DEPOSITO_BANCARIO = 5,
  CREDITO_EM_CONTA = 6,
  PIX = 7,
  NENHUM = 99,
}

enum Status {
  PENDING = 0,
  PAID = 1,
  CANCELED = 2,
  CHARGEBACK = 3,
  ESTORNADO = 4,
  EM_ANALISE = 5,
  AGUARDANDO_ESTORNO = 6,
  PROCESSANDO_CARTAO = 7,
  PARCIALMENTE_PAGO = 8,
  BLOQUEADO = 9,
  FAILED = 10,
  DUPLICADO = 11,
  CARRINHO_ABANDONADO = 80,
  DESCONHECIDO = 99,
}

interface GenerateInvoiceBody {
  apiToken: string
  id?: string
  planId: string
  agentEmail?: string
  dontCancelPrevious?: boolean
  value?: number
  freight?: number // valor do frete
  freightType?: string // tipo de frete
  totalValue?: string
  metadata?: {}
  paymentType: PaymentType | string | number
  status: Status | string | number
  customEvent?: number
  paymentLine?: number
  date?: Date
  clientName?: string
  clientEmail?: string
  clientPhoneNumber: string
  clientDocument?: string
  clientAddress?: string
  clientAddressNumber?: string
  clientAddressComp?: string
  clientAddressDistrict?: string
  clientAddressCity?: string
  clientAddressState?: string
  clientAddressCountry?: string
  clientZipCode?: string
  checkoutUrl?: string
  boletoUrl?: string
  pixQrCode?: string
  pixUrl?: string
}

export class Voxuy extends BaseService implements ITriggerWebhook {
  protected validateSchema = {
    'header.app.action': (value) =>
      !!value && ['bindTriggerWebhook'].includes(value),
    'header.app.integration.api_webhook': (value) => !!value,
    'header.app.integration.api_key': (value) => !!value,
    'payload.data.subscriber_name': (value) => !!value,
    'payload.data.subscriber_email': (value) => !!value,
  }

  async bindTriggerWebhook(payload: Payload): Promise<void> {
    const { api_key: apiKey, api_webhook: apiWebhook } =
      payload.header.app.integration
    const { planId } = payload.header.app.integration.metadata

    const {
      subscriber_name: subscriberName,
      subscriber_document_number: subscriberDocument,
      subscriber_email: subscriberEmail,
      subscriber_phone: subscriberPhone,
      subscriber_zipcode: subscriberZipcode = null,
      subscriber_street: subscriberStreet = null,
      subscriber_number: subscriberNumber = null,
      subscriber_comp: subscriberComplement = null,
      subscriber_district: subscriberDistrict = null,
      subscriber_city: subscriberCity = null,
      subscriber_state: subscriberState = null,
      subscriber_country: subscriberCountry = null,
      payment_type: paymentType,
      payment_status: paymentStatus,
    } = payload.payload.data

    const body: GenerateInvoiceBody = {
      apiToken: apiKey,
      planId,
      paymentType: paymentType ? PaymentType[paymentType.toUpperCase()] : PaymentType.NENHUM,
      status: paymentStatus ? (paymentStatus === 'canceled' ? Status.ESTORNADO : Status[paymentStatus.toUpperCase()]) : Status.CARRINHO_ABANDONADO,
      date: new Date(),
      clientName: subscriberName,
      clientEmail: subscriberEmail,
      clientPhoneNumber: subscriberPhone,
      clientDocument: onlyNumbers(subscriberDocument),
      clientAddress: subscriberStreet,
      clientAddressNumber: subscriberNumber,
      clientAddressComp: subscriberComplement,
      clientAddressDistrict: subscriberDistrict,
      clientAddressCity: subscriberCity,
      clientAddressState: subscriberState,
      clientAddressCountry: subscriberCountry,
      clientZipCode: subscriberZipcode,
    }

    await axios.post(apiWebhook, body, {
      headers: {
        'Content-Type': 'application/json',
      },
    })
  }
}
