import { Builderall } from '../builderall'
import { builderallPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import FormData from 'form-data'
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

const searchSubscriberMock = {
  data: {
    data: {
      subscriber_uid: 'anyid',
      email: 'anyemail@email.com',
      status: 'anystatus'
    },
  },

  status: 200

}

jest.mock('axios', () => ({
  create: jest.fn().mockReturnThis(),
  post: jest.fn((url) => {
    switch (url) {
      case '/lists/subscribers/create/any-api-token':
      case '/lists/subscribers/delete/any-api-token':
        return { data: { subscriber_uid: 'any-id' }, status: 200 }

      case '/lists/subscribers/search-by-email/any-api-token':
        return searchSubscriberMock

      default:
        return 'unexpected-resource'
    }
  })
}))

describe('Webhook Service', () => {
  let service: Builderall

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Builderall(new ValidateJsAdapter(), builderallPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    it('should call method correctly', async() => {
      builderallPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(
        '/lists/subscribers/create/any-api-token',
        expect.any(FormData),
        expect.objectContaining({ headers: { 'content-type': expect.any(String) } }))
      expect(response).toBeUndefined()
    })

    it('should throw error if response status is undefined', async() => {
      builderallPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementationOnce(() => undefined)
      await expect(service.process()).rejects.toThrowError('Error on create subscriber')
      expect(spyOnAxios).toBeCalledTimes(1)
    })

    it('should throw dinamic error if axios req with data.status has error', async () => {
      builderallPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementationOnce(async () => ({ data: { status: 'error', error: 'anyerror' } }))
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      await expect(service.process()).rejects.toThrowError('anyerror')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
    })

    it('should throw specific error if axios req status is not 200', async () => {
      builderallPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementationOnce(async () => ({ status: 'any' }))
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      await expect(service.process()).rejects.toThrowError('Error on create subscriber')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
    })
  })

  describe('bindRemoveContact', () => {
    it('should call method correctly', async() => {
      builderallPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(2)
      expect(spyOnAxios).toHaveBeenNthCalledWith(
        1,
        '/lists/subscribers/search-by-email/any-api-token',
        expect.any(FormData),
        expect.objectContaining({ headers: { 'content-type': expect.any(String) } }))

      expect(spyOnAxios).toHaveBeenNthCalledWith(
        2,
        '/lists/subscribers/delete/any-api-token',
        expect.any(FormData),
        expect.objectContaining({ headers: { 'content-type': expect.any(String) } }))
      expect(response).toBeUndefined()
    })

    it('should throw specific error if /search-by-email axios response is not with expected format', async() => {
      builderallPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementationOnce(
        async (url) => url === '/lists/subscribers/search-by-email/any-api-token' && ({ data: { any: 'any' }, status: 200 }))
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      await expect(service.process()).rejects.toThrowError("Subscriber doesn't exist")
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
    })

    it('should throw dinamic error if /search-by-email axios req has status error', async() => {
      builderallPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementationOnce(
        async (url) => url === '/lists/subscribers/search-by-email/any-api-token' && ({ data: { status: 'error', error: 'anyerror' } }))
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      await expect(service.process()).rejects.toThrowError('anyerror')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
    })

    it('should throw specific error if /search-by-email axios req status is not 200', async() => {
      builderallPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementationOnce(
        async (url) => url === '/lists/subscribers/search-by-email/any-api-token' && ({ status: 'any' }))
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      await expect(service.process()).rejects.toThrowError('Error on search subscriber')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
    })

    it('should throw specific error if /search-by-email axios response is undefined', async() => {
      builderallPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementationOnce(
        async (url) => url === '/lists/subscribers/search-by-email/any-api-token' && (undefined))
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      await expect(service.process()).rejects.toThrowError('Error on search subscriber')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
    })

    it('should throw dinamic error if /delete axios req has status error', async() => {
      builderallPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementation(
        async (url) => {
          switch (url) {
            case '/lists/subscribers/delete/any-api-token':
              return { data: { status: 'error', error: 'anyerror' } }

            default:
              return searchSubscriberMock
          }
        })
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      await expect(service.process()).rejects.toThrowError('anyerror')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(2)
    })

    it('should throw specific error if /delete axios req status is not 200', async() => {
      builderallPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementation(
        async (url) => {
          switch (url) {
            case '/lists/subscribers/delete/any-api-token':
              return { status: 'any' }

            default:
              return searchSubscriberMock
          }
        })
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      await expect(service.process()).rejects.toThrowError('Error on remove subscriber')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(2)
    })

    it('should throw specific error if /delete axios req status is undefined', async() => {
      builderallPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'post').mockImplementation(
        async (url) => {
          switch (url) {
            case '/lists/subscribers/delete/any-api-token':
              return undefined

            default:
              return searchSubscriberMock
          }
        })
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      await expect(service.process()).rejects.toThrowError('Error on remove subscriber')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(builderallPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(2)
    })
  })
})
