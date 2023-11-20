import { Webhook } from '../webhook'
import { webhookPayload } from './services.mock'
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

describe('Webhook Service', () => {
  let service: Webhook

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Webhook(new ValidateJsAdapter(), webhookPayload)
      expect(service).toBeDefined()
    })
  })

  describe('bindTriggerWebhook', () => {
    const { header: { app: { integration: { api_webhook } } } } = webhookPayload

    const expectedPayload = {
      ...webhookPayload.payload.data,
      payment_plans: [1234,12345],
      payment_date: '12-24-1998'
    }

    it('should make axios post correctly', async() => {
      service = new Webhook(new ValidateJsAdapter(), webhookPayload)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindWebhook = jest.spyOn(service, 'bindTriggerWebhook')
      const response = await service.process()
      expect(spyOnBindWebhook).toBeCalledTimes(1)
      expect(spyOnBindWebhook).toBeCalledWith(webhookPayload)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(api_webhook, expectedPayload, { headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-Hub-Signature': 'anyhex' } })
      expect(response).toBeDefined()
      expect(response).toStrictEqual({ any: 'anyres' })
    })

    it('should make axios post correctly default date', async() => {
      webhookPayload.payload.data.payment_date = undefined
      service = new Webhook(new ValidateJsAdapter(), webhookPayload)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindWebhook = jest.spyOn(service, 'bindTriggerWebhook')
      const response = await service.process()
      expect(spyOnBindWebhook).toBeCalledTimes(1)
      expect(spyOnBindWebhook).toBeCalledWith(webhookPayload)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(api_webhook, {
        ...expectedPayload,
        payment_date: expect.any(Date)
      }, { headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-Hub-Signature': 'anyhex' } })
      expect(response).toBeDefined()
      expect(response).toStrictEqual({ any: 'anyres' })
    })
  })
})
