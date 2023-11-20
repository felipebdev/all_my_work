import { Wisenotas } from '../wisenotas'
import { wiseNotasPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import crypto from 'crypto'
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  post: jest.fn((url) => {
    switch (url) {
      default:
        return { any: 'anyres' }
    }
  }),
}))

jest.mock('crypto', () => ({
  createHmac: jest.fn().mockReturnThis(),
  update: jest.fn().mockReturnThis(),
  digest: jest.fn(() => 'anyhex'),
  randomBytes: jest.requireActual('crypto').randomBytes
}))

describe('Wisenotas Service', () => {
  let service: Wisenotas

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Wisenotas(new ValidateJsAdapter(), wiseNotasPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindGenerateInvoice', () => {
    const { header: { app: { integration: { api_webhook } } } } = wiseNotasPayloadMock

    it('should call bindGenerateInvoice correctly with cnpj values', async () => {
      wiseNotasPayloadMock.payload.data.payment_status = 'paid'
      wiseNotasPayloadMock.header.app.action = 'bindGenerateInvoice'
      wiseNotasPayloadMock.payload.data.client_holder_name = undefined
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(wiseNotasPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(api_webhook, expect.objectContaining({ status: 'APROVADA' }), { headers: { 'X-Hub-Signature': 'anyhex' } })
      expect(response).toBeDefined()
      expect(response).toStrictEqual({ any: 'anyres' })
    })

    it('should call bindGenerateInvoice correctly with cpf values values', async () => {
      wiseNotasPayloadMock.payload.data.payment_status = 'paid'
      wiseNotasPayloadMock.header.app.action = 'bindGenerateInvoice'
      wiseNotasPayloadMock.payload.data.client_cpf = '507.834.268-01'
      wiseNotasPayloadMock.payload.data.client_cnpj = undefined
      wiseNotasPayloadMock.header.app.integration.metadata = undefined
      wiseNotasPayloadMock.payload.data.client_holder_name = 'Any Client Holder Name'
      wiseNotasPayloadMock.payload.data.client_company_name = undefined
      wiseNotasPayloadMock.payload.data.subscriber_country = 'BRA'
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindGenerateInvoice')
      const response = await service.process()
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(wiseNotasPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(api_webhook, expect.objectContaining({ status: 'APROVADA' }), { headers: { 'X-Hub-Signature': 'anyhex' } })
      expect(response).toBeDefined()
      expect(response).toStrictEqual({ any: 'anyres' })
    })

    it('should throw expected error if payment_status is different than paid', async () => {
      wiseNotasPayloadMock.payload.data.payment_status = 'any'
      wiseNotasPayloadMock.header.app.action = 'bindGenerateInvoice'
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindGenerate = jest.spyOn(service, 'bindGenerateInvoice')
      await expect(service.process()).rejects.toThrowError('Payment status must be paid')
      expect(spyOnBindGenerate).toBeCalledTimes(1)
      expect(spyOnBindGenerate).toBeCalledWith(wiseNotasPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(0)
    })
  })

  describe('bindCancelInvoice', () => {
    const { header: { app: { integration: { api_webhook } } } } = wiseNotasPayloadMock

    it('should call bindCancelInvoice correctly', async () => {
      wiseNotasPayloadMock.payload.data.payment_status = 'canceled'
      wiseNotasPayloadMock.header.app.action = 'bindCancelInvoice'
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindCancel = jest.spyOn(service, 'bindCancelInvoice')
      const response = await service.process()
      expect(spyOnBindCancel).toBeCalledTimes(1)
      expect(spyOnBindCancel).toBeCalledWith(wiseNotasPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(api_webhook, expect.objectContaining({ status: 'CANCELADA' }), { headers: { 'X-Hub-Signature': 'anyhex' } })
      expect(response).toBeDefined()
      expect(response).toStrictEqual({ any: 'anyres' })
    })

    it('should throw expected error if payment_status is different than canceled', async () => {
      wiseNotasPayloadMock.payload.data.payment_status = 'any'
      wiseNotasPayloadMock.header.app.action = 'bindCancelInvoice'
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindCancel = jest.spyOn(service, 'bindCancelInvoice')
      await expect(service.process()).rejects.toThrowError('Payment status must be canceled')
      expect(spyOnBindCancel).toBeCalledTimes(1)
      expect(spyOnBindCancel).toBeCalledWith(wiseNotasPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(0)
    })
  })
})
