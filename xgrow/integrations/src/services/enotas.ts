import axios from 'axios'
import Invoice from '../database/mongodb/collections/invoice'
import { IGenerateInvoice } from '../contracts/actions'
import { ICancelInvoice } from '../contracts/actions/cancel-invoice'
import { InvoiceFactory } from '../database/mongodb/factories/invoice.factory'
import { Payload } from '../job'
import { onlyNumbers } from '../utils/helper'
import { BaseService } from './base'

interface Request {
  url: string
  headers: {
    Accept: string
    'Content-Type': string
    Authorization: string
  }
}

interface CreateClientBody {
  email: string
  telefone: string // onlyNumbers
  nome: string
  cpfCnpj: string // onlyNumbers
  endereco?: {
    cidade?: string
    codigoIbgeCidade?: number
    logradouro?: string
    numero?: number
    complemento?: string
    bairro?: string
    cep?: string // onlyNumbers
  }
}

interface Client {
  id: string
  email?: string
  name?: string
}

enum PaymentType {
  none = 0,
  boleto = 1,
  credit_card = 2,
  deposit = 3,
  pix = 4,
  cash = 5,
  paypal = 6,
  other = 7
}
interface GenerateInvoiceBody {
  cliente: {
    id: string
  }
  data: string
  vencimento?: string
  produto: {
    idExterno: string
    nome: string
    valorToral: number
    diasGarantia: number
    tags?: string // "tag1;tag2..."
  }
  valorTotal: number
  quandoEmitirNFe: number // 1 = AposAGarantia
  enviarNFeCliente?: boolean
  meioPagamento: string
  tags?: string // "tag1;tag2..."
  municipioPrestacao?: {
    nome: string
    codigoibge?: number
  }
  dataCompetencia?: Date
  discriminacao?: string
  valorTotalNFe?: number
  observacoes?: string
}

export class Enotas extends BaseService implements IGenerateInvoice, ICancelInvoice {
  private static readonly BASE_URL = 'https://app.enotas.com.br/api'
  private static readonly HTTP_ACCEPT = 'application/json'
  private static readonly CONTENT_TYPE = 'application/json'
  private readonly invoiceFactory: InvoiceFactory = new InvoiceFactory()

  protected validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindGenerateInvoice', 'bindCancelInvoice'].includes(value)),
    'header.app.integration.api_key': (value: string) => !!value,
    'header.app.integration.metadata.process_after_days': (value: number) => !!value,
  }

  private request (payload: Payload): Request {
    const { api_key } = payload.header.app.integration

    const request: Request = {
      url: Enotas.BASE_URL,
      headers: {
        Accept: Enotas.HTTP_ACCEPT,
        'Content-Type': Enotas.CONTENT_TYPE,
        Authorization: `Basic ${api_key}`
      }
    }

    return request
  }

  public async bindGenerateInvoice(payload: Payload): Promise<any> {
    const request = this.request(payload)

    const payloadData = payload.payload.data

    const createClientBody: CreateClientBody = {
      nome: payloadData.subscriber_name,
      email: payloadData.subscriber_email,
      telefone: payloadData.subscriber_phone ? onlyNumbers(payloadData.subscriber_phone) : '',
      cpfCnpj: onlyNumbers(payloadData.subscriber_document_number),
      endereco: {
        cep: payloadData.subscriber_zipcode ? onlyNumbers(payloadData.subscriber_zipcode) : '',
        logradouro: payloadData.subscriber_street,
        numero: payloadData.subscriber_number,
        bairro: payloadData.subscriber_district,
        complemento: payloadData.subscriber_comp,
        cidade: payloadData.subscriber_city,
      },
    }
    const client = await this.createOrUpdateClient(request, createClientBody)

    const { process_after_days } = payload.header.app.integration.metadata

    const generatedInvoices = []

    for (const product of payloadData.payment_plans) {
      const generateInvoiceBody: GenerateInvoiceBody = {
        cliente: client,
        data: new Date(payloadData.payment_date).toISOString(),
        produto: {
          idExterno: String(product.id),
          nome: product.plan,
          valorToral: +product.price,
          diasGarantia: process_after_days,
        },
        municipioPrestacao: {
          nome: payloadData.client_city
        },
        valorTotal: +payloadData.payment_plans_value,
        quandoEmitirNFe: 1,
        enviarNFeCliente: true,
        meioPagamento: PaymentType[payloadData.payment_type],
      }

      const generatedInvoice = await this.generateInvoice(request, generateInvoiceBody)
      await this.invoiceFactory.createInvoice(payload, generatedInvoice.vendaId, product.id)
      generatedInvoices.push({ ...generatedInvoice, productId: generateInvoiceBody.produto.idExterno })
    }

    return generatedInvoices
  }

  public async bindCancelInvoice (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const payloadData = payload.payload.data

    const canceledInvoices = []

    for (const product of payloadData.payment_plans) {
      const invoice = await Invoice.findOne({ transactionId: payloadData.payment_order_code, productId: product.id })
      if (!invoice) {
        throw new Error('Invoice not found')
      }
      const canceledInvoice = await this.cancelInvoice(request, invoice.invoiceId)
      canceledInvoices.push({ ...canceledInvoice, productId: product.id })
      await Invoice.updateOne(
        { transactionId: payloadData.payment_order_code, productId: product.id },
        { status: payload.header.app.action }
      )
    }
  }

  async createOrUpdateClient(request: Request, createClientBody: CreateClientBody): Promise<Client> {
    const { data: createdClient } = await axios.post(
      `${request.url}/clientes`,
      createClientBody,
      { headers: request.headers }
    )

    return { id: createdClient.clienteId }
  }

  async generateInvoice(request: Request, generateInvoiceBody: GenerateInvoiceBody): Promise<{ vendaId: string }> {
    const { data: generatedInvoice } = await axios.post(
      `${request.url}/vendas`,
      generateInvoiceBody,
      { headers: request.headers }
    )

    return generatedInvoice
  }

  async cancelInvoice(request: Request, invoiceId: string): Promise<{ vendaId: string }> {
    const { data: canceledInvoice } = await axios.post(`${request.url}/vendas/${invoiceId}/cancelar`, {}, { headers: request.headers })

    return canceledInvoice
  }
}
