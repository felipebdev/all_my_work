import { IGenerateInvoice, ICancelInvoice } from '@app/contracts/actions'
import { BaseService } from './base'
import { Payload } from '@app/job'
import { InvoiceFactory } from '../database/mongodb/factories/invoice.factory'
import { onlyNumbers, checkForNull } from '../utils/helper'
import Invoice from '../database/mongodb/collections/invoice'
import axios from 'axios'
import moment from 'moment'
import ILogable from '../contracts/logable'
import WinstonLog from '../providers/winston'

enum MethodsUrl {
  CREATE = '/create_nfe_55',
  CANCEL = '/cancel_nfe_55',
}

enum SubscriberPersonType {
  LEGAL = 'J',
  NATURAL = 'F',
  FOREIGNER = 'E'
}

interface DocumentProduct {
  [index: string]: {
    DOCUMENT_PRODUCT_COD: string
    DOCUMENT_PRODUCT_NAME: string
    DOCUMENT_PRODUCT_QTD: string
    DOCUMENT_PRODUCT_UNITARY_VALUE: string
  }
}

interface MailSend {
  [index: string]: {
    EMAIL: string
  }
}

interface GenerateInvoiceBody {
  DESTINATION_NAME: string
  DESTINATION_TAXID: string
  DESTINATION_TAXTYPE: SubscriberPersonType
  DESTINATION_STREET: string
  DESTINATION_NUMBER: string
  DESTINATION_COMPLEMENT?: string
  DESTINATION_DISTRICT: string
  DESTINATION_CITY: string
  DESTINATION_UF: string
  DESTINATION_ZIPCODE: string
  DESTINATION_PHONE?: string
  DESTINATION_EMAIL?: string
  DOCUMENT_BASEVALUE?: string
  DOCUMENT_PRODUCT?: DocumentProduct
  DESTINATION_EMAIL_SEND?: MailSend
  EXTERNAL_ID?: string
  SALE_ID?: string
  DOCUMENT_ISSUE_DATE?: string
  REQUEST_ORIGIN?: string
}

interface CancelInvoiceBody {
  REASON: string
  DOCUMENT_ID: string
  EXTERNAL_ID?: string
}

/**
 * {@link https://app.notazz.com/docs/api/notazz-swagger-ui.php}
 */
export class Notazz extends BaseService implements IGenerateInvoice, ICancelInvoice {
  private static readonly BASE_URL = 'https://app.notazz.com/api'
  private static readonly REQUEST_ORIGIN = 'xgrow-platform'
  private readonly invoiceFactory: InvoiceFactory = new InvoiceFactory()
  private readonly logger: ILogable = WinstonLog.getInstance()
  protected validateSchema = {
    'header.app.action': (value) =>
      !!value && ['bindGenerateInvoice', 'bindCancelInvoice'].includes(value),
    'header.app.planIds': (value) => value.length !== 0,
    'header.app.integration.api_key': (value) => !!value,
    'payload.data.payment_plans': (value) => !!value && value.length !== 0,
    'payload.data.payment_order_code': (value) => !!value,
    'payload.data.payment_plans_value': (value) => !!value
  }

  async bindGenerateInvoice (payload: Payload): Promise<void> {
    const {
      header:
      {
        app: { integration: { api_key } }
      },
      payload: { data: { payment_plans, payment_order_code } }
    } = payload

    const partialInvoiceBody: GenerateInvoiceBody = this.setInvoiceBody(payload)

    let err: boolean

    for (const product of payment_plans) {
      const DOCUMENT_PRODUCT: DocumentProduct = {
        1: {
          DOCUMENT_PRODUCT_COD: String(product.id),
          DOCUMENT_PRODUCT_NAME: String(product.plan),
          DOCUMENT_PRODUCT_QTD: '1',
          DOCUMENT_PRODUCT_UNITARY_VALUE: Number(product.price).toFixed(2).toString()
        }
      }

      const DOCUMENT_BASEVALUE = Number(product.price).toFixed(2).toString()

      const EXTERNAL_ID = String(payment_order_code).concat(`/${product.id}`)

      const invoiceBody: GenerateInvoiceBody = {
        ...partialInvoiceBody,
        DOCUMENT_PRODUCT,
        DOCUMENT_BASEVALUE,
        EXTERNAL_ID
      }

      const { id: invoiceId, statusProcessamento, motivo } = await this.generateInvoice(invoiceBody, api_key)

      if (statusProcessamento !== 'sucesso') {
        err = true
        this.logger.error(`Notazz: Erro ao tentar gerar NF-e: ${motivo}`)
      }

      await this.invoiceFactory.createInvoice(payload, invoiceId, product.id)
    }

    if (err) throw new Error('Notazz: Error ao tentar gerar NF-e')
  }

  async bindCancelInvoice (payload: Payload): Promise<void> {
    const { api_key } = payload.header.app.integration

    const { payment_order_code: transactionId, payment_plans } = payload.payload.data

    let err: boolean

    for (const product of payment_plans) {
      const { invoiceId: DOCUMENT_ID = null } = await Invoice.findOne({ transactionId, productId: product.id })

      if (!DOCUMENT_ID) {
        err = true
        this.logger.error(`Notazz: Invoice not found in MongoDB. ProductId: ${product.id}. Transaction: ${transactionId}`)
      } else {
        const invoiceBody: CancelInvoiceBody = {
          DOCUMENT_ID,
          REASON: 'Estorno requerido pelo cliente na plataforma Xgrow'
        }
        const { statusProcessamento, motivo } = await this.cancelInvoice(invoiceBody, api_key)
        if (statusProcessamento !== 'sucesso') {
          err = true
          this.logger.error(`Notazz: Erro ao tentar cancelar NF-e: ${motivo}`)
        }

        await Invoice.updateOne(
          { transactionId, productId: product.id },
          { status: payload.header.app.action }
        )
      }
    }
    if (err) throw new Error('Notazz: Erro ao tentar cancelar NF-e')
  }

  private setInvoiceBody(payload: Payload): GenerateInvoiceBody {
    const {
      subscriber_name: DESTINATION_NAME,
      subscriber_document_type,
      subscriber_document_number: DESTINATION_TAXID,
      subscriber_phone: DESTINATION_PHONE = '',
      subscriber_email: DESTINATION_EMAIL,
      subscriber_country,
      subscriber_email
    } = payload.payload.data

    const {
      metadata: { process_after_days = null } = {}
    } = payload.header.app.integration

    const DOCUMENT_ISSUE_DATE = ![null, undefined, '', 0, '0'].includes(process_after_days)
      ? moment().add(process_after_days, 'days').format('YYYY-MM-DD HH:mm:ss')
      : undefined

    const DESTINATION_EMAIL_SEND: MailSend = {
      1: {
        EMAIL: subscriber_email
      }
    }

    const documentType = {
      CPF: SubscriberPersonType.NATURAL,
      CNPJ: SubscriberPersonType.LEGAL
    }

    const DESTINATION_TAXTYPE: SubscriberPersonType = subscriber_country === 'BRA'
      ? documentType[subscriber_document_type]
      : SubscriberPersonType.FOREIGNER

    const {
      DESTINATION_STREET,
      DESTINATION_NUMBER,
      DESTINATION_COMPLEMENT,
      DESTINATION_DISTRICT,
      DESTINATION_CITY,
      DESTINATION_UF,
      DESTINATION_ZIPCODE
    } = this.setAddress(payload)

    const { REQUEST_ORIGIN } = Notazz

    return {
      DESTINATION_NAME,
      DESTINATION_TAXID: onlyNumbers(DESTINATION_TAXID),
      DESTINATION_TAXTYPE,
      DESTINATION_STREET,
      DESTINATION_NUMBER,
      DESTINATION_COMPLEMENT,
      DESTINATION_DISTRICT,
      DESTINATION_CITY,
      DESTINATION_UF,
      DESTINATION_ZIPCODE,
      DESTINATION_PHONE: onlyNumbers(DESTINATION_PHONE),
      DESTINATION_EMAIL,
      REQUEST_ORIGIN,
      DESTINATION_EMAIL_SEND,
      DOCUMENT_ISSUE_DATE
    }
  }

  private setAddress(payload: Payload): any {
    const {
      subscriber_street = null,
      subscriber_number = null,
      subscriber_comp = null,
      subscriber_district = null,
      subscriber_city = null,
      subscriber_state = null,
      subscriber_zipcode = null,
      client_address,
      client_number,
      client_complement,
      client_district,
      client_city,
      client_state,
      client_zipcode
    } = payload.payload.data

    const subscriberAddress = {
      DESTINATION_STREET: subscriber_street,
      DESTINATION_NUMBER: subscriber_number,
      DESTINATION_COMPLEMENT: subscriber_comp,
      DESTINATION_DISTRICT: subscriber_district,
      DESTINATION_CITY: subscriber_city,
      DESTINATION_UF: subscriber_state,
      DESTINATION_ZIPCODE: onlyNumbers(subscriber_zipcode),
    }

    const clientAddress = {
      DESTINATION_STREET: client_address,
      DESTINATION_NUMBER: client_number,
      DESTINATION_COMPLEMENT: client_complement,
      DESTINATION_DISTRICT: client_district,
      DESTINATION_CITY: client_city,
      DESTINATION_UF: client_state,
      DESTINATION_ZIPCODE: onlyNumbers(client_zipcode),
    }

    if (checkForNull(subscriberAddress)) return clientAddress

    return subscriberAddress
  }

  private async generateInvoice(invoiceBody: GenerateInvoiceBody, apiKey: string): Promise<any> {
    const { data } = await axios.post(
      `${Notazz.BASE_URL}${MethodsUrl.CREATE}`,
      invoiceBody,
      {
        headers: {
          API_KEY: apiKey
        }
      }
    )

    return data
  }

  private async cancelInvoice(invoiceBody: CancelInvoiceBody, apiKey: string): Promise<any> {
    const { data } = await axios.post(
      `${Notazz.BASE_URL}${MethodsUrl.CANCEL}`,
      invoiceBody,
      {
        headers: {
          API_KEY: apiKey
        }
      }
    )

    return data
  }
}
