import axios from 'axios'
import { IGenerateInvoice } from '../contracts/actions'
import { Payload } from '../job'
import { onlyNumbers, parseToBrDate } from '../utils/helper'
import { BaseService } from './base'

enum InvoiceAction {
  PAID = 1,
  CANCELED = 2,
}

interface GenerateInvoiceBody {
  chave: string
  produtor: {
    razaoSocial?: string
    documento: number
  }
  venda: {
    idTransacao: string
    status: InvoiceAction
    dataPagamento: string
    garantia: number
    codProduto: string
    nomeProduto: string
    valorVenda: string
  }
  cliente: {
    nome: string
    documento: string
    email: string
    telefone?: string
    logradouro?: string
    numero?: string
    complemento?: string
    bairro?: string
    cep?: string
    cidade?: string
    estado?: string
    pais?: string
  }
}

/**
 * @see /docs/smartnotas.pdf
 */
export class Smartnotas extends BaseService implements IGenerateInvoice {
  private static readonly DEFAULT_PROCESS_AFTER_DAYS = 7
  protected validateSchema = {
    'header.app.action': value => (!!value && ['bindGenerateInvoice'].includes(value)),
    'header.app.planIds': value => value.length !== 0,
    'header.app.platform_id': value => !!value,
    'header.app.integration.api_webhook': value => !!value,
    'payload.data.subscriber_name': value => !!value,
    'payload.data.subscriber_document_number': value => !!value,
    'payload.data.subscriber_email': value => !!value,
    'payload.data.payment_status': value => !!value,
    'payload.data.payment_order_code': value => !!value,
    'payload.data.payment_date': value => !!value,
    'payload.data.payment_plans': value => value.length !== 0
  }

  public async bindGenerateInvoice (payload: Payload): Promise<void> {
    const {
      planIds,
      platform_id: platformId,
      integration: {
        api_webhook: apiWebhook,
        metadata: {
          process_after_days: processAfterDays = null
        } = {}
      }
    } = payload.header.app

    const {
      client_cpf: clientCpf = null,
      client_cnpj: clientCnpj = null,
      client_fantasy_name: clientFantasyName,
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
      payment_status: paymentStatus,
      payment_order_code: paymentOrderCode,
      payment_date: paymentDate,
      payment_plans: paymentPlans,
      payment_installment_number: paymentInstallmentNumber
    } = payload.payload.data

    for (const plan of paymentPlans) {
      if (planIds.includes(Number(plan.id))) {
        const body: GenerateInvoiceBody = {
          chave: platformId,
          produtor: {
            documento: (
              clientCpf
                ? Number(onlyNumbers(clientCpf))
                : Number(onlyNumbers(clientCnpj))
            ),
            razaoSocial: clientFantasyName
          },
          venda: {
            idTransacao: `${paymentOrderCode}_${plan.type}_${plan.id}${paymentInstallmentNumber ? '_' + String(paymentInstallmentNumber) : ''}`,
            status: Number(InvoiceAction[paymentStatus.toUpperCase()]),
            dataPagamento: parseToBrDate(paymentDate),
            garantia: Number(processAfterDays) || Smartnotas.DEFAULT_PROCESS_AFTER_DAYS,
            codProduto: String(plan.id),
            nomeProduto: String(plan.plan),
            valorVenda: String(plan.price)
          },
          cliente: {
            nome: subscriberName,
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
            pais: subscriberCountry
          }
        }
        await axios.post(`${apiWebhook}`, body)
      }
    }
  }
}
