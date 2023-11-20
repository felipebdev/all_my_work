import axios from 'axios'
import { IGenerateInvoice } from '../contracts/actions'
import { Payload } from '../job'
import { onlyNumbers } from '../utils/helper'
import { BaseService } from './base'
import crypto from 'crypto'

enum Status {
  APPROVED = 'APROVADA',
  CANCELED = 'CANCELADA'
}

enum PaymentType {
  credit_card = 'CartaoDeCredito',
  pix = 'PIX',
  transfer = 'Transferencia',
  cash = 'Dinheiro',
  boleto = 'Boleto',
  other = 'Outros'
}

interface InvoiceBody {
  apiKey: string
  idTransacao: string
  produtor: {
    documento: string
    razaoSocial: string
  }
  status: Status
  dataVenda?: string
  dataPagamento: string
  dataReemboso?: string
  garantia: number
  formaPagamento: PaymentType

  valorTotalVenda: number
  items: Array<{
    codProduto: string
    nomeProduto: string
    valorVenda: number
    valorTaxas: number
    coprodutores?: Array<{
      documento: string
      razaoSocial: string
      comissao: number
    }>
  }>
  cliente: {
    nome: string
    tipoDocumento: string
    documento: string
    email: string
    telefone?: string
    logradouro?: string
    numero?: number
    complemento?: string
    bairro?: string
    cep?: string
    cidade?: string
    estado?: string
    pais: string
    estrangeiro: boolean
  }
}

/**
 * @see /docs/wisenotas.pdf
 */
export class Wisenotas extends BaseService implements IGenerateInvoice {
  private static readonly DEFAULT_PROCESS_AFTER_DAYS = 7
  protected validateSchema = {
    'header.app.action': (value) =>
      !!value && ['bindGenerateInvoice', 'bindCancelInvoice'].includes(value),
    'header.app.planIds': (value) => value.length !== 0,
    'header.app.integration.api_webhook': (value) => !!value,
    'header.app.integration.api_key': (value) => !!value,
    'payload.data.subscriber_name': (value) => !!value,
    'payload.data.subscriber_document_number': (value) => !!value,
    'payload.data.subscriber_email': (value) => !!value,
    'payload.data.payment_order_code': (value) => !!value,
    'payload.data.payment_date': (value) => !!value,
    'payload.data.payment_plans': (value) => value.length !== 0,
  }

  public async bindGenerateInvoice(payload: Payload): Promise<any> {
    const {
      integration: {
        api_webhook: apiWebhook,
        api_key: apiKey,
      },
    } = payload.header.app

    const invoiceStatus = payload.payload.data.payment_status

    if (invoiceStatus !== 'paid') {
      throw new Error('Payment status must be paid')
    }

    const invoiceBody: InvoiceBody = this.setInvoiceBody(payload, Status.APPROVED)

    return await this.sendRequest(invoiceBody, apiWebhook, apiKey)
  }

  public async bindCancelInvoice (payload: Payload): Promise<any> {
    const {
      integration: {
        api_webhook: apiWebhook,
        api_key: apiKey,
      },
    } = payload.header.app

    const invoiceStatus = payload.payload.data.payment_status

    if (invoiceStatus !== 'canceled') {
      throw new Error('Payment status must be canceled')
    }

    const invoiceBody: InvoiceBody = this.setInvoiceBody(payload, Status.CANCELED)

    return await this.sendRequest(invoiceBody, apiWebhook, apiKey)
  }

  private assignRequest(apiKey: string, body: any): string {
    return crypto
      .createHmac('sha1', apiKey)
      .update(`${apiKey}${body.idTransacao}`)
      .digest('hex')
  }

  private async sendRequest(
    invoiceBody: InvoiceBody, apiWebhook: string, apiKey: string
  ): Promise<any> {
    return await axios.post(`${apiWebhook}`, invoiceBody, {
      headers: {
        'X-Hub-Signature': this.assignRequest(apiKey, invoiceBody),
      },
    })
  }

  private setInvoiceBody(payload: Payload, status: Status): InvoiceBody {
    const {
      integration: {
        api_key: apiKey,
        metadata: { process_after_days: processAfterDays = null } = {},
      }
    } = payload.header.app

    const {
      client_cpf: clientCpf = null,
      client_cnpj: clientCnpj = null,
      client_company_name: clientCompanyName = null,
      client_holder_name: clientHolderName = null,
      subscriber_name: subscriberName,
      subscriber_document_type: subscriberDocumentType,
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
      payment_order_code: paymentOrderCode,
      payment_date: paymentDate,
      payment_plans: paymentPlans,
      payment_type: paymentType,
      payment_plans_value: paymentTotal
    } = payload.payload.data

    return {
      apiKey: apiKey,
      idTransacao: paymentOrderCode,
      produtor: {
        documento: clientCpf
          ? String(onlyNumbers(clientCpf))
          : String(onlyNumbers(clientCnpj)),
        razaoSocial: clientCpf
          ? clientHolderName
          : clientCompanyName,
      },
      status,
      dataPagamento: new Date(paymentDate).toISOString(),
      valorTotalVenda: +paymentTotal,
      formaPagamento: PaymentType[paymentType],
      garantia: Number(processAfterDays) || Wisenotas.DEFAULT_PROCESS_AFTER_DAYS,
      items: paymentPlans.map((plan) => ({
        codProduto: String(plan.id),
        nomeProduto: String(plan.plan),
        valorVenda: String(plan.price),
        valorTaxas: String(+plan.price_plus_fees - +plan.price),
        coprodutores: Array.from<any>(plan.coproducers || []).filter((coprod) => coprod.issue_invoice).map((coprod) => ({
          documento: onlyNumbers(coprod.document),
          razaoSocial: coprod.name,
          comissao: Number(coprod.invoice_percent)
        }))
      })),
      cliente: {
        nome: subscriberName,
        tipoDocumento: subscriberDocumentType,
        documento: onlyNumbers(subscriberDocument),
        email: subscriberEmail,
        telefone: subscriberPhone,
        logradouro: subscriberStreet,
        numero: subscriberNumber,
        complemento: subscriberComplement,
        bairro: subscriberDistrict,
        cep: onlyNumbers(subscriberZipcode),
        cidade: subscriberCity,
        estado: subscriberState,
        pais: subscriberCountry ?? 'BRA',
        estrangeiro: subscriberCountry ? subscriberCountry !== 'BRA' : false
      }
    }
  }
}
