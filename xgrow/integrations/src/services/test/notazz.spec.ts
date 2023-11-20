/* eslint-disable @typescript-eslint/no-unused-vars */
import { Notazz } from '../notazz'
import { notazzPayloadMock } from './services.mock';
import axios from 'axios'
import { InvoiceFactory } from '../../database/mongodb/factories/invoice.factory'
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'
import { Payload } from '@app/job'
import Invoice from '../../database/mongodb/collections/invoice'

jest.mock('axios', () => ({
  create: jest.fn().mockReturnThis(),
  post: jest.fn((url) => {
    switch (url) {
      case 'https://app.notazz.com/api/create_nfe_55':
        return { data: { id: 'any-id-created', statusProcessamento: 'sucesso', motivo: '' } }
      case 'https://app.notazz.com/api/cancel_nfe_55':
        return { data: { id: 'any-id-canceled', statusProcessamento: 'sucesso', motivo: '' } }
    }
  })
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
    updateOne: jest.fn(() => ({ invoiceId: 'anyInvoiceId' })),
  }
})

describe('Notazz Service', () => {
  let service: Notazz

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Notazz(new ValidateJsAdapter(), notazzPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindGenerateInvoice', () => {
    it('should generate invoice and return id and status', async () => {
      const payload: Payload = {
        ...notazzPayloadMock,
        header: {
          ...notazzPayloadMock.header,
          app: {
            ...notazzPayloadMock.header.app,
            integration: {
              ...notazzPayloadMock.header.app.integration,
              metadata: {
                ...notazzPayloadMock.header.app.integration.metadata,
                process_after_days: 1
              }
            }
          }
        }
      }
      payload.header.app.action = 'bindGenerateInvoice'
      const service = new Notazz(new ValidateJsAdapter(), payload)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(payload)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        'https://app.notazz.com/api/create_nfe_55',
        {
          DESTINATION_NAME: 'John Doe Xgrow',
          DESTINATION_TAXID: '17261626058',
          DESTINATION_TAXTYPE: 'F',
          DESTINATION_STREET: 'Alguma Rua',
          DESTINATION_NUMBER: '245',
          DESTINATION_COMPLEMENT: 'Algum Complemento',
          DESTINATION_DISTRICT: 'Algum Bairro',
          DESTINATION_CITY: 'Indaiatuba',
          DESTINATION_UF: 'SP',
          DESTINATION_ZIPCODE: '13340551',
          DESTINATION_PHONE: '24998786654',
          DESTINATION_EMAIL: 'felipebdev@gmail.com',
          DOCUMENT_BASEVALUE: '6000.00',
          DOCUMENT_PRODUCT: {
            1: {
              DOCUMENT_PRODUCT_COD: '4692',
              DOCUMENT_PRODUCT_NAME: 'Teste Xgrow Notazz',
              DOCUMENT_PRODUCT_QTD: '1',
              DOCUMENT_PRODUCT_UNITARY_VALUE: '6000.00'
            },
          },
          EXTERNAL_ID: '1234512345/4692',
          REQUEST_ORIGIN: 'xgrow-platform',
          DESTINATION_EMAIL_SEND: {
            1: {
              EMAIL: 'felipebdev@gmail.com'
            }
          },
          DOCUMENT_ISSUE_DATE: expect.any(String)
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        'https://app.notazz.com/api/create_nfe_55',
        {
          DESTINATION_NAME: 'John Doe Xgrow',
          DESTINATION_TAXID: '17261626058',
          DESTINATION_TAXTYPE: 'F',
          DESTINATION_STREET: 'Alguma Rua',
          DESTINATION_NUMBER: '245',
          DESTINATION_COMPLEMENT: 'Algum Complemento',
          DESTINATION_DISTRICT: 'Algum Bairro',
          DESTINATION_CITY: 'Indaiatuba',
          DESTINATION_UF: 'SP',
          DESTINATION_ZIPCODE: '13340551',
          DESTINATION_PHONE: '24998786654',
          DESTINATION_EMAIL: 'felipebdev@gmail.com',
          DOCUMENT_BASEVALUE: '6000.00',
          DOCUMENT_PRODUCT: {
            1: {
              DOCUMENT_PRODUCT_COD: '4693',
              DOCUMENT_PRODUCT_NAME: 'Teste Xgrow Notazz 2',
              DOCUMENT_PRODUCT_QTD: '1',
              DOCUMENT_PRODUCT_UNITARY_VALUE: '6000.00'
            }
          },
          EXTERNAL_ID: '1234512345/4693',
          REQUEST_ORIGIN: 'xgrow-platform',
          DESTINATION_EMAIL_SEND: {
            1: {
              EMAIL: 'felipebdev@gmail.com'
            }
          },
          DOCUMENT_ISSUE_DATE: expect.any(String)
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )
      expect(response).toBeUndefined()
    })

    it('should generate invoice with client address if subscriber address has null props', async () => {
      notazzPayloadMock.header.app.action = 'bindGenerateInvoice'
      const payload: Payload = {
        ...notazzPayloadMock,
        payload: {
          data: {
            ...notazzPayloadMock.payload.data,
            subscriber_street: undefined,
            subscriber_number: undefined,
            subscriber_comp: undefined,
            subscriber_district: undefined,
            subscriber_city: undefined,
            subscriber_state: undefined,
            subscriber_zipcode: undefined,
            subscriber_phone: undefined,
            subscriber_country: 'ANY'
          }
        }
      }
      const service = new Notazz(new ValidateJsAdapter(), payload)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(payload)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        'https://app.notazz.com/api/create_nfe_55',
        {
          DESTINATION_NAME: 'John Doe Xgrow',
          DESTINATION_TAXID: '17261626058',
          DESTINATION_TAXTYPE: 'E',
          DESTINATION_STREET: 'Rua Ipiranga',
          DESTINATION_NUMBER: '111',
          DESTINATION_COMPLEMENT: '',
          DESTINATION_DISTRICT: 'Vila Barros',
          DESTINATION_CITY: 'Barueri',
          DESTINATION_UF: 'SP',
          DESTINATION_ZIPCODE: '06410250',
          DESTINATION_PHONE: undefined,
          DESTINATION_EMAIL: 'felipebdev@gmail.com',
          DOCUMENT_BASEVALUE: '6000.00',
          DOCUMENT_PRODUCT: {
            1: {
              DOCUMENT_PRODUCT_COD: '4692',
              DOCUMENT_PRODUCT_NAME: 'Teste Xgrow Notazz',
              DOCUMENT_PRODUCT_QTD: '1',
              DOCUMENT_PRODUCT_UNITARY_VALUE: '6000.00'
            },
          },
          EXTERNAL_ID: '1234512345/4692',
          REQUEST_ORIGIN: 'xgrow-platform',
          DESTINATION_EMAIL_SEND: {
            1: {
              EMAIL: 'felipebdev@gmail.com'
            }
          }
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        'https://app.notazz.com/api/create_nfe_55',
        {
          DESTINATION_NAME: 'John Doe Xgrow',
          DESTINATION_TAXID: '17261626058',
          DESTINATION_TAXTYPE: 'E',
          DESTINATION_STREET: 'Rua Ipiranga',
          DESTINATION_NUMBER: '111',
          DESTINATION_COMPLEMENT: '',
          DESTINATION_DISTRICT: 'Vila Barros',
          DESTINATION_CITY: 'Barueri',
          DESTINATION_UF: 'SP',
          DESTINATION_ZIPCODE: '06410250',
          DESTINATION_PHONE: undefined,
          DESTINATION_EMAIL: 'felipebdev@gmail.com',
          DOCUMENT_BASEVALUE: '6000.00',
          DOCUMENT_PRODUCT: {
            1: {
              DOCUMENT_PRODUCT_COD: '4693',
              DOCUMENT_PRODUCT_NAME: 'Teste Xgrow Notazz 2',
              DOCUMENT_PRODUCT_QTD: '1',
              DOCUMENT_PRODUCT_UNITARY_VALUE: '6000.00'
            }
          },
          EXTERNAL_ID: '1234512345/4693',
          REQUEST_ORIGIN: 'xgrow-platform',
          DESTINATION_EMAIL_SEND: {
            1: {
              EMAIL: 'felipebdev@gmail.com'
            }
          }
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )
      expect(response).toBeUndefined()
    })

    it('should throw exception if invoice status is not sucesso', async () => {
      notazzPayloadMock.header.app.action = 'bindGenerateInvoice'
      const spyOnAxiosPost = jest.spyOn(axios, 'post').mockImplementationOnce(async () => (
        { data: { id: 'any-id-created', statusProcessamento: 'any', motivo: 'algum motivo' } }
      ))
      const spyOnBindGenerate = jest.spyOn(service, 'bindGenerateInvoice')
      await expect(service.process()).rejects.toThrowError('Notazz: Error ao tentar gerar NF-e')
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(notazzPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        'https://app.notazz.com/api/create_nfe_55',
        {
          DESTINATION_NAME: 'John Doe Xgrow',
          DESTINATION_TAXID: '17261626058',
          DESTINATION_TAXTYPE: 'F',
          DESTINATION_STREET: 'Alguma Rua',
          DESTINATION_NUMBER: '245',
          DESTINATION_COMPLEMENT: 'Algum Complemento',
          DESTINATION_DISTRICT: 'Algum Bairro',
          DESTINATION_CITY: 'Indaiatuba',
          DESTINATION_UF: 'SP',
          DESTINATION_ZIPCODE: '13340551',
          DESTINATION_PHONE: '24998786654',
          DESTINATION_EMAIL: 'felipebdev@gmail.com',
          DOCUMENT_BASEVALUE: '6000.00',
          DOCUMENT_PRODUCT: {
            1: {
              DOCUMENT_PRODUCT_COD: '4692',
              DOCUMENT_PRODUCT_NAME: 'Teste Xgrow Notazz',
              DOCUMENT_PRODUCT_QTD: '1',
              DOCUMENT_PRODUCT_UNITARY_VALUE: '6000.00'
            },
          },
          EXTERNAL_ID: '1234512345/4692',
          REQUEST_ORIGIN: 'xgrow-platform',
          DESTINATION_EMAIL_SEND: {
            1: {
              EMAIL: 'felipebdev@gmail.com'
            }
          }
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )

      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        'https://app.notazz.com/api/create_nfe_55',
        {
          DESTINATION_NAME: 'John Doe Xgrow',
          DESTINATION_TAXID: '17261626058',
          DESTINATION_TAXTYPE: 'F',
          DESTINATION_STREET: 'Alguma Rua',
          DESTINATION_NUMBER: '245',
          DESTINATION_COMPLEMENT: 'Algum Complemento',
          DESTINATION_DISTRICT: 'Algum Bairro',
          DESTINATION_CITY: 'Indaiatuba',
          DESTINATION_UF: 'SP',
          DESTINATION_ZIPCODE: '13340551',
          DESTINATION_PHONE: '24998786654',
          DESTINATION_EMAIL: 'felipebdev@gmail.com',
          DOCUMENT_BASEVALUE: '6000.00',
          DOCUMENT_PRODUCT: {
            1: {
              DOCUMENT_PRODUCT_COD: '4693',
              DOCUMENT_PRODUCT_NAME: 'Teste Xgrow Notazz 2',
              DOCUMENT_PRODUCT_QTD: '1',
              DOCUMENT_PRODUCT_UNITARY_VALUE: '6000.00'
            },
          },
          EXTERNAL_ID: '1234512345/4693',
          REQUEST_ORIGIN: 'xgrow-platform',
          DESTINATION_EMAIL_SEND: {
            1: {
              EMAIL: 'felipebdev@gmail.com'
            }
          }
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )
    })
  })

  describe('bindCancelInvoice', () => {
    it('should cancel invoice', async () => {
      notazzPayloadMock.header.app.action = 'bindCancelInvoice'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindCancelInvoice')
      const response = await service.process()
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(notazzPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        'https://app.notazz.com/api/cancel_nfe_55',
        {
          DOCUMENT_ID: 'anyInvoiceId',
          REASON: 'Estorno requerido pelo cliente na plataforma Xgrow'
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        'https://app.notazz.com/api/cancel_nfe_55',
        {
          DOCUMENT_ID: 'anyInvoiceId',
          REASON: 'Estorno requerido pelo cliente na plataforma Xgrow'
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )
      expect(response).toBeUndefined()
    })

    it('should throw exception if Mongo does not find invoice', async () => {
      notazzPayloadMock.header.app.action = 'bindCancelInvoice'
      jest.spyOn(Invoice, 'findOne').mockImplementation(() => ({}))
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindCancelInvoice')
      await expect(service.process()).rejects.toThrowError('Notazz: Erro ao tentar cancelar NF-e')
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(notazzPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(0)
    })

    it('should throw exception if statusProcessamento is not sucesso', async () => {
      notazzPayloadMock.header.app.action = 'bindCancelInvoice'
      const spyOnAxiosPost = jest.spyOn(axios, 'post').mockImplementationOnce(async () => (
        { data: { id: 'any-id-created', statusProcessamento: 'any', motivo: 'algum motivo' } }
      ))
      jest.spyOn(Invoice, 'findOne').mockImplementation(() => ({ invoiceId: 'anyInvoiceId' }))
      const spyOnBindCancel = jest.spyOn(service, 'bindCancelInvoice')
      await expect(service.process()).rejects.toThrowError('Notazz: Erro ao tentar cancelar NF-e')
      expect(spyOnBindCancel).toBeCalledTimes(1)
      expect(spyOnBindCancel).toBeCalledWith(notazzPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://app.notazz.com/api/cancel_nfe_55',
        {
          DOCUMENT_ID: 'anyInvoiceId',
          REASON: 'Estorno requerido pelo cliente na plataforma Xgrow'
        },
        { headers: { API_KEY: 'mynotazzapikey' } }
      )
    })
  })
})
