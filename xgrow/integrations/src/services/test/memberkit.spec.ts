import { Memberkit } from '../memberkit'
import { memberkitPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  get: jest.fn(async() => Promise.resolve({ data: { any: 'anyres' } })),
  post: jest.fn(() => ({ data: { any: 'anyres' } })),
  put: jest.fn(() => ({ data: { any: 'anyres' } })),
  delete: jest.fn(() => ({ data: { any: 'anyres' } })),

}))

describe('Memberkit Service', () => {
  let service: Memberkit

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Memberkit(new ValidateJsAdapter(), memberkitPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    it('should call method correctly and update existing contact', async() => {
      const { header: { app: { integration: { api_key } } } } = memberkitPayloadMock
      memberkitPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(memberkitPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledTimes(0)
      expect(spyOnAxiosGet).toBeCalledWith(
        'https://memberkit.com.br/api/v1/users/subscriber@email.com',
        {
          headers: {
            Accept: 'application/json',
          },
          params: { api_key }
        }
      )
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://memberkit.com.br/api/v1/users/subscriber@email.com',
        expect.objectContaining({
          metadata: {
            cpf_cnpj: '507.834.268-00',
            phone_number: '982867373',
            phone_local_code: '19'
          },
          // classroom_ids: [NaN] ? TODO: pode?
        }),
        {
          headers: {
            Accept: 'application/json',
          },
          params: { api_key }
        }
      )

      expect(response).toBeUndefined()
    })

    it('should call method correctly and create non-existent contact', async() => {
      const { header: { app: { integration: { api_key } } } } = memberkitPayloadMock
      memberkitPayloadMock.header.app.metadata = { list: '1' }
      memberkitPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosGet = jest.spyOn(axios, 'get').mockImplementationOnce(async () => Promise.reject(new Error('any')))
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(memberkitPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledTimes(0)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        'https://memberkit.com.br/api/v1/users/subscriber@email.com',
        {
          headers: {
            Accept: 'application/json',
          },
          params: { api_key }
        }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://memberkit.com.br/api/v1/users',
        expect.objectContaining({
          cpf_cnpj: '507.834.268-00',
          phone_number: '982867373',
          phone_local_code: '19',
          full_name: 'John Doe',
          email: 'subscriber@email.com'
        }),
        {
          headers: {
            Accept: 'application/json',
          },
          params: { api_key }
        }
      )

      expect(response).toBeUndefined()
    })
  })

  describe('bindRemoveContact', () => {
    it('should call method correctly', async() => {
      const { header: { app: { integration: { api_key } } } } = memberkitPayloadMock
      memberkitPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(memberkitPayloadMock)
      expect(spyOnAxiosDelete).toBeCalledTimes(1)
      expect(spyOnAxiosDelete).toBeCalledWith(
        'https://memberkit.com.br/api/v1/users/subscriber@email.com',
        {
          headers: {
            Accept: 'application/json',
          },
          params: { api_key }
        }
      )
      expect(response).toBeUndefined()
    })
  })
})
