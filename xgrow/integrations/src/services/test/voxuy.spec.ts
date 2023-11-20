import { Voxuy } from '../voxuy'
import { voxuyPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  post: jest.fn((url) => {
    switch (url) {
      default:
        return { any: 'anyres' }
    }
  }),
}))

describe('Voxuy Service', () => {
  let service: Voxuy

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Voxuy(new ValidateJsAdapter(), voxuyPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindTriggerWebhook', () => {
    const { header: { app: { integration: { api_webhook } } } } = voxuyPayloadMock

    it('should call bindGrantAccess correctly', async() => {
      service = new Voxuy(new ValidateJsAdapter(), voxuyPayloadMock)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindTrigger = jest.spyOn(service, 'bindTriggerWebhook')
      const response = await service.process()
      expect(spyOnBindTrigger).toBeCalledTimes(1)
      expect(spyOnBindTrigger).toBeCalledWith(voxuyPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(api_webhook, expect.objectContaining({ planId: '1',clientName: 'subscriberName' }), { headers: { 'Content-Type': 'application/json' } })
      expect(response).toBeUndefined()
    })

    it('should call bindGrantAccess correctly with default payment type and status', async() => {
      voxuyPayloadMock.payload.data.payment_type = undefined
      voxuyPayloadMock.payload.data.payment_status = undefined
      service = new Voxuy(new ValidateJsAdapter(), voxuyPayloadMock)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindTrigger = jest.spyOn(service, 'bindTriggerWebhook')
      const response = await service.process()
      expect(spyOnBindTrigger).toBeCalledTimes(1)
      expect(spyOnBindTrigger).toBeCalledWith(voxuyPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(api_webhook, expect.objectContaining({ planId: '1',clientName: 'subscriberName', paymentType: 99, status: 80 }), { headers: { 'Content-Type': 'application/json' } })
      expect(response).toBeUndefined()
    })

    it('should call bindGrantAccess correctly with default payment type and status', async() => {
      voxuyPayloadMock.payload.data.payment_type = undefined
      voxuyPayloadMock.payload.data.payment_status = 'canceled'
      service = new Voxuy(new ValidateJsAdapter(), voxuyPayloadMock)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindTrigger = jest.spyOn(service, 'bindTriggerWebhook')
      const response = await service.process()
      expect(spyOnBindTrigger).toBeCalledTimes(1)
      expect(spyOnBindTrigger).toBeCalledWith(voxuyPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(api_webhook, expect.objectContaining({ planId: '1',clientName: 'subscriberName', paymentType: 99, status: 4 }), { headers: { 'Content-Type': 'application/json' } })
      expect(response).toBeUndefined()
    })
  })
})
