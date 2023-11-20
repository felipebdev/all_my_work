import { Enotas } from '../enotas'
import { enotasPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { InvoiceFactory } from '../../database/mongodb/factories/invoice.factory'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import Invoice from '../../database/mongodb/collections/invoice'

jest.mock('axios', () => ({
  post: jest.fn((url) => {
    switch (url) {
      case 'https://app.enotas.com.br/api/clientes':
        return { data: { clienteId: 'anycreateclientid' } }
      case 'https://app.enotas.com.br/api/vendas':
        return { data: { vendaId: 'anyNotaId', vendaName: 'anyNotaName' } }
      default:
        return { any: 'unexpected resource' }
    }
  }),
}))

const invoiceFactoryMock: InvoiceFactory = {
  createInvoice: jest.fn()
}

jest.mock('../../database/mongodb/factories/invoice.factory', () => {
  return {
    InvoiceFactory: jest.fn().mockImplementation(() => ({
      createInvoice: invoiceFactoryMock.createInvoice
    }))
  }
})

jest.mock('../../database/mongodb/collections/invoice', () => {
  return {
    findOne: jest.fn(() => ({ invoiceId: 'anyInvoiceId' })),
    updateOne: jest.fn()
  }
})

describe('Enotas Service', () => {
  let service: Enotas
  const { header: { app: { integration: { api_key } } } } = enotasPayloadMock
  const expectedAxiosHeaders = {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    Authorization: `Basic ${api_key}`
  }

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Enotas(new ValidateJsAdapter(), enotasPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindGenerateInvoice', () => {
    it('should assert axios and mongoose calls', async() => {
      enotasPayloadMock.header.app.action = 'bindGenerateInvoice'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(enotasPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        'https://app.enotas.com.br/api/clientes',
        {
          nome: 'John Doe',
          email: 'subscriber@email.com',
          telefone: '19982867373',
          cpfCnpj: '50783426801',
          endereco: {
            cep: '13340501' ,
            logradouro: 'Rua Any',
            numero: '245',
            bairro: 'Bairro',
            complemento: 'Any Comp',
            cidade: 'Indaiatuba',
          },
        },
        { headers: expectedAxiosHeaders }
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        'https://app.enotas.com.br/api/vendas',
        {
          cliente: { id: 'anycreateclientid' },
          data: new Date('12-24-2022').toISOString(),
          produto: {
            idExterno: 'anyproductid',
            nome: 'anyproductname',
            valorToral: 1000,
            diasGarantia: 2,
          },
          municipioPrestacao: {
            nome: 'Campinas'
          },
          valorTotal: 1000,
          quandoEmitirNFe: 1,
          enviarNFeCliente: true,
          meioPagamento: 1,
        },
        { headers: expectedAxiosHeaders }
      )
      expect(invoiceFactoryMock.createInvoice).toBeCalledTimes(1)
      expect(invoiceFactoryMock.createInvoice).toBeCalledWith(
        enotasPayloadMock,
        'anyNotaId',
        'anyproductid'
      )
      expect(response).toStrictEqual([
        {
          vendaId: 'anyNotaId',
          vendaName: 'anyNotaName',
          productId: 'anyproductid'
        }
      ])
    })

    it('should assert axios and mongoose calls', async() => {
      enotasPayloadMock.header.app.action = 'bindGenerateInvoice'
      enotasPayloadMock.payload.data.subscriber_phone = undefined
      enotasPayloadMock.payload.data.subscriber_zipcode = undefined
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(enotasPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        'https://app.enotas.com.br/api/clientes',
        {
          nome: 'John Doe',
          email: 'subscriber@email.com',
          telefone: '',
          cpfCnpj: '50783426801',
          endereco: {
            cep: '' ,
            logradouro: 'Rua Any',
            numero: '245',
            bairro: 'Bairro',
            complemento: 'Any Comp',
            cidade: 'Indaiatuba',
          },
        },
        { headers: expectedAxiosHeaders }
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        'https://app.enotas.com.br/api/vendas',
        {
          cliente: { id: 'anycreateclientid' },
          data: new Date('12-24-2022').toISOString(),
          produto: {
            idExterno: 'anyproductid',
            nome: 'anyproductname',
            valorToral: 1000,
            diasGarantia: 2,
          },
          municipioPrestacao: {
            nome: 'Campinas'
          },
          valorTotal: 1000,
          quandoEmitirNFe: 1,
          enviarNFeCliente: true,
          meioPagamento: 1,
        },
        { headers: expectedAxiosHeaders }
      )
      expect(invoiceFactoryMock.createInvoice).toBeCalledTimes(1)
      expect(invoiceFactoryMock.createInvoice).toBeCalledWith(
        enotasPayloadMock,
        'anyNotaId',
        'anyproductid'
      )
      expect(response).toStrictEqual([
        {
          vendaId: 'anyNotaId',
          vendaName: 'anyNotaName',
          productId: 'anyproductid'
        }
      ])
    })
  })

  describe('bindCancelInvoice', () => {
    it('should assert axios calls', async() => {
      enotasPayloadMock.header.app.action = 'bindCancelInvoice'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindCancelInvoice')
      const spyOnInvoiceFind = jest.spyOn(Invoice, 'findOne')
      const spyOnInvoiceUpdate = jest.spyOn(Invoice,'updateOne')
      const response = await service.process()
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(enotasPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnInvoiceFind).toBeCalledTimes(1)
      expect(spyOnInvoiceUpdate).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://app.enotas.com.br/api/vendas/anyInvoiceId/cancelar',
        {},
        { headers: expectedAxiosHeaders }
      )
      expect(response).toBeUndefined()
    })
    it('should throw specific error if Invoice.findOne is empty', async() => {
      enotasPayloadMock.header.app.action = 'bindCancelInvoice'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindCancelInvoice')
      const spyOnInvoiceFind = jest.spyOn(Invoice, 'findOne').mockImplementationOnce(() => (null))
      const spyOnInvoiceUpdate = jest.spyOn(Invoice,'updateOne')
      await expect(service.process()).rejects.toThrowError('Invoice not found')
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(enotasPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(0)
      expect(spyOnInvoiceUpdate).toBeCalledTimes(0)
      expect(spyOnInvoiceFind).toBeCalledTimes(1)
    })
  })
})
