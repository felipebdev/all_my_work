import { Smartnotas } from '../smartnotas'
import { smartnotasPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'
import { Payload } from '@app/job'

jest.mock('axios', () => ({
  post: jest.fn(() => ({ data: { id: '1234' } })),
}))

describe('Smartnotas Service', () => {
  let service: Smartnotas

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Smartnotas(new ValidateJsAdapter(), smartnotasPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindGenerateInvoice', () => {
    const { header: { app: { integration: { api_webhook } } } } = smartnotasPayloadMock

    it('should call axios correctly with cpf values', async() => {
      smartnotasPayloadMock.header.app.action = 'bindGenerateInvoice'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(smartnotasPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        api_webhook,
        expect.objectContaining({
          chave: 'anyplatformid',
          produtor: expect.objectContaining({
            documento: 50783426801,
            razaoSocial: 'John Doe'
          }),
          venda: expect.objectContaining({
            garantia: 3,
            idTransacao: 'anyordercode_anypaymenttype_122_anyname',
          }),
          cliente: expect.objectContaining({
            nome: 'John Doe',
            documento: '550956543',
            email: 'subscriber@email.com',
            telefone: '19982867373',
            logradouro: null,
            numero: null,
            complemento: null,
            bairro: null,
            cep: undefined,
            cidade: null,
            estado: null,
            pais: null
          })
        })
      )
      expect(response).toBeUndefined()
    })

    it('should call axios correctly with cpf values', async() => {
      smartnotasPayloadMock.header.app.action = 'bindGenerateInvoice'
      smartnotasPayloadMock.payload.data.payment_installment_number = undefined
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(smartnotasPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        api_webhook,
        expect.objectContaining({

          venda: expect.objectContaining({
            garantia: 3,
            idTransacao: 'anyordercode_anypaymenttype_122',
          }),
        })
      )
      expect(response).toBeUndefined()
    })

    it('should call axios correctly with cnpj values', async() => {
      const payload: Payload = {
        ...smartnotasPayloadMock,
        header: {
          ...smartnotasPayloadMock.header,
          app: {
            ...smartnotasPayloadMock.header.app,
            action: 'bindGenerateInvoice',
          }
        },
        payload: {
          data: {
            ...smartnotasPayloadMock.payload.data,
            client_cnpj: '909808707000100',
            client_cpf: undefined
          }
        }
      }
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const service = new Smartnotas(new ValidateJsAdapter(), payload)
      const spyOnBindInsert = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payload)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        api_webhook,
        expect.objectContaining({
          chave: 'anyplatformid',
          produtor: expect.objectContaining({
            documento: 909808707000100,
            razaoSocial: 'John Doe'
          }),
          venda: expect.objectContaining({
            garantia: 3
          })
        })
      )
      expect(response).toBeUndefined()
    })

    it('should generate invoice with default "garantia"', async() => {
      const payload: Payload = {
        ...smartnotasPayloadMock,
        header: {
          ...smartnotasPayloadMock.header,
          app: {
            ...smartnotasPayloadMock.header.app,
            action: 'bindGenerateInvoice',
            integration: {
              ...smartnotasPayloadMock.header.app.integration,
              metadata: {
                processAfterDays: 'any'
              }
            }
          }
        }
      }
      const service = new Smartnotas(new ValidateJsAdapter(), payload)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payload)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        api_webhook,
        expect.objectContaining({
          chave: 'anyplatformid',
          produtor: expect.objectContaining({
            documento: 50783426801,
            razaoSocial: 'John Doe',
          }),
          venda: expect.objectContaining({
            garantia: 7
          })
        })
      )
      expect(response).toBeUndefined()
    })

    it('should generate invoice without metadata"', async() => {
      const payload: Payload = {
        ...smartnotasPayloadMock,
        header: {
          ...smartnotasPayloadMock.header,
          app: {
            ...smartnotasPayloadMock.header.app,
            action: 'bindGenerateInvoice',
            integration: {
              ...smartnotasPayloadMock.header.app.integration,
              metadata: undefined
            }
          }
        }
      }
      const service = new Smartnotas(new ValidateJsAdapter(), payload)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payload)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        api_webhook,
        expect.objectContaining({
          chave: 'anyplatformid',
          produtor: expect.objectContaining({
            documento: 50783426801,
            razaoSocial: 'John Doe',
          }),
          venda: expect.objectContaining({
            garantia: 7
          })
        })
      )
      expect(response).toBeUndefined()
    })
  })
})
