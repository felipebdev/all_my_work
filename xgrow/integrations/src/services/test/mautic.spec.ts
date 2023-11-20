import { Mautic } from '../mautic'
import { mauticPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  get: jest.fn(() => ({ data: { contacts: [{ id: 'anyid' }] } })),
  put: jest.fn(() => ({ data: { contact: { id: 'anyid' } } })),
  post: jest.fn(() => ({ data: { any: 'anyres' } })),
  delete: jest.fn(() => ({ data: { any: 'anyres' } }))
}))

describe('Mautic Service', () => {
  let service: Mautic

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Mautic(new ValidateJsAdapter(), mauticPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    const { header: { app: { integration: { api_webhook } } } } = mauticPayloadMock

    it('should call method correctly', async() => {
      mauticPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(mauticPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `${api_webhook}/api/contacts`,
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
          params: {
            search: 'subscriber@email.com'
          }
        }
      )
      expect(spyOnAxiosPut).toBeCalledWith(
        `${api_webhook}/api/contacts/anyid/edit`,
        {
          email: 'subscriber@email.com',
          firstname: 'John',
          lastname: 'Doe',
          phone: '19982867373'
        },
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
        }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        `${api_webhook}/api/segments/9191/contact/anyid/add`,
        {},
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
        }
      )
      expect(response).toBeUndefined()
    })

    it('should call method correctly and add to contact 0', async() => {
      mauticPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosPost = jest.spyOn(axios, 'post').mockImplementationOnce(async (url) => ({
        data: undefined
      }))
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(mauticPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `${api_webhook}/api/contacts`,
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
          params: {
            search: 'subscriber@email.com'
          }
        }
      )
      expect(spyOnAxiosPut).toBeCalledWith(
        `${api_webhook}/api/contacts/anyid/edit`,
        {
          email: 'subscriber@email.com',
          firstname: 'John',
          lastname: 'Doe',
          phone: '19982867373'
        },
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
        }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        `${api_webhook}/api/segments/9191/contact/anyid/add`,
        {},
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
        }
      )
      expect(response).toBeUndefined()
    })

    it('should add to contact with id 0', async() => {
      mauticPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put').mockImplementationOnce(async (url) => ({
        data: {}
      }))
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(mauticPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        `${api_webhook}/api/segments/9191/contact/0/add`,
        {},
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
        }
      )
      expect(response).toBeUndefined()
    })

    it('should add to contact with id 0', async() => {
      mauticPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxiosGet = jest.spyOn(axios, 'get').mockImplementationOnce(async (url) => ({
        data: { contacts: [] }
      }))
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(mauticPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        `${api_webhook}/api/segments/9191/contact/anyid/add`,
        {},
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
        }
      )
      expect(response).toBeUndefined()
    })
  })

  describe('bindRemoveContact', () => {
    const { header: { app: { integration: { api_webhook } } } } = mauticPayloadMock

    it('should call method correctly', async() => {
      mauticPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindRemove = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindRemove).toBeCalledTimes(1)
      expect(spyOnBindRemove).toBeCalledWith(mauticPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosDelete).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `${api_webhook}/api/contacts`,
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
          params: {
            search: 'subscriber@email.com'
          }
        }
      )
      expect(spyOnAxiosDelete).toBeCalledWith(
        `${api_webhook}/api/contacts/anyid/delete`,
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          }
        }
      )
      expect(response).toBeUndefined()
    })
  })
})
