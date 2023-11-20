import { Cademi } from '../cademi'
import { cademiPayload } from './services.mock'
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

describe('Cademi Service', () => {
  let service: Cademi

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Cademi(new ValidateJsAdapter(), cademiPayload)
      expect(service).toBeDefined()
    })
  })

  describe('bindGrantAccess', () => {
    const { header: { app: { integration: { api_webhook } } } } = cademiPayload

    it('should call bindGrantAccess correctly', async() => {
      cademiPayload.header.app.action = 'bindGrantAccess'
      cademiPayload.payload.data.subscriber_phone = undefined

      service = new Cademi(new ValidateJsAdapter(), cademiPayload)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindGrant = jest.spyOn(service, 'bindGrantAccess')
      const response = await service.process()
      expect(spyOnBindGrant).toBeCalledTimes(1)
      expect(spyOnBindGrant).toBeCalledWith(cademiPayload)
      expect(spyOnAxios).toBeCalledTimes(2)
      expect(spyOnAxios).toHaveBeenNthCalledWith(
        1,
        api_webhook,
        expect.objectContaining({ status: 'aprovado', produto_id: '1', cliente_celular: undefined }))
      expect(spyOnAxios).toHaveBeenNthCalledWith(
        2,
        api_webhook,
        expect.objectContaining({ status: 'aprovado', produto_id: '2' }))
      expect(response).toBeUndefined()
    })
  })

  describe('bindRevokeAccess', () => {
    const { header: { app: { integration: { api_webhook } } } } = cademiPayload

    it('should call bindRevokeAccess correctly', async() => {
      cademiPayload.header.app.action = 'bindRevokeAccess'

      service = new Cademi(new ValidateJsAdapter(), cademiPayload)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindRevoke = jest.spyOn(service, 'bindRevokeAccess')
      const response = await service.process()
      expect(spyOnBindRevoke).toBeCalledTimes(1)
      expect(spyOnBindRevoke).toBeCalledWith(cademiPayload)
      expect(spyOnAxios).toBeCalledTimes(2)
      expect(spyOnAxios).toHaveBeenNthCalledWith(
        1,
        api_webhook,
        expect.objectContaining({ status: 'cancelado', produto_id: '1' }))
      expect(spyOnAxios).toHaveBeenNthCalledWith(
        2,
        api_webhook,
        expect.objectContaining({ status: 'cancelado', produto_id: '2' }))
      expect(response).toBeUndefined()
    })
  })
})
