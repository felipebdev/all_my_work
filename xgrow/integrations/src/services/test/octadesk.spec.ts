import { Octadesk } from '../octadesk'
import { octadeskPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  get: jest.fn(() => ({ data: { id: '1234' } })),
  post: jest.fn(() => ({ data: { any: 'any-res' } }))
}))

describe('Octadesk Service', () => {
  let service: Octadesk
  const { header: { app: { integration: { api_secret } } } } = octadeskPayloadMock
  const expectedAxiosHeader = {
    Accept: 'application/json',
    Authorization: `Bearer ${api_secret}`
  }

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Octadesk(new ValidateJsAdapter(), octadeskPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    it('should create contact if it doesnt exists by axios exception', async() => {
      octadeskPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosGet = jest.spyOn(axios, 'get').mockImplementationOnce(() => {
        // eslint-disable-next-line @typescript-eslint/no-throw-literal
        throw { any: 'any' }
      })
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(octadeskPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        'https://api.octadesk.services/persons?email=subscriber@email.com',
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://api.octadesk.services/persons',
        expect.objectContaining({
          email: 'subscriber@email.com',
          name: 'John Doe',
          customerCode: 'subscriberId',
          phoneContacts: {
            number: '19982867373',
            type: 1
          },
        }),
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })

    it('should create contact if it doesnt exists by axios null response', async() => {
      octadeskPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosGet = jest.spyOn(axios, 'get').mockImplementationOnce(async () => {
        // eslint-disable-next-line @typescript-eslint/no-throw-literal
        return { any: 'any' }
      })
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(octadeskPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        'https://api.octadesk.services/persons?email=subscriber@email.com',
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://api.octadesk.services/persons',
        expect.objectContaining({
          email: 'subscriber@email.com',
          name: 'John Doe',
          customerCode: 'subscriberId',
          phoneContacts: {
            number: '19982867373',
            type: 1
          },
        }),
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })

    it('should not create contact if it already exists ', async() => {
      octadeskPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(octadeskPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        'https://api.octadesk.services/persons?email=subscriber@email.com',
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledTimes(0)
      expect(response).toBeUndefined()
    })
  })
})
