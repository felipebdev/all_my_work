import { Kajabi } from '../kajabi'
import { kajabiPayloadMock } from './services.mock'
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

describe('Kajabi Service', () => {
  let service: Kajabi

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Kajabi(new ValidateJsAdapter(), kajabiPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindGrantAccess', () => {
    it('should call bindGrantAccess correctly', async() => {
      kajabiPayloadMock.header.app.action = 'bindGrantAccess'
      service = new Kajabi(new ValidateJsAdapter(), kajabiPayloadMock)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindGrant = jest.spyOn(service, 'bindGrantAccess')
      const response = await service.process()
      expect(spyOnBindGrant).toBeCalledTimes(1)
      expect(spyOnBindGrant).toBeCalledWith(kajabiPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith('cademi.com?send_offer_grant_email=true', {
        name: 'subscriberName',
        email: 'subscriber@email.com',
        external_user_id: '1234'
      })
      expect(response).toStrictEqual({ any: 'anyres' })
    })
  })

  describe('bindRevokeAccess', () => {
    it('should call bindRevokeAccess correctly', async() => {
      kajabiPayloadMock.header.app.action = 'bindRevokeAccess'
      service = new Kajabi(new ValidateJsAdapter(), kajabiPayloadMock)
      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindRevoke = jest.spyOn(service, 'bindRevokeAccess')
      const response = await service.process()
      expect(spyOnBindRevoke).toBeCalledTimes(1)
      expect(spyOnBindRevoke).toBeCalledWith(kajabiPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith('cademi.com?send_offer_grant_email=true', {
        name: 'subscriberName',
        email: 'subscriber@email.com',
        external_user_id: '1234'
      })
      expect(response).toStrictEqual({ any: 'anyres' })
    })
  })
})
