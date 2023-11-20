import { Pipedrive } from '../pipedrive'
import { pipedrivePayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  get: jest.fn(() => ({ data: { data: { items: [{ item: { id: 'anypersonid' } }] } } })),
  post: jest.fn(() => ({ data: { id: '1234' } })),
  delete: jest.fn(() => ({ data: { any: 'anyres' } }))
}))

describe('Pipedrive Service', () => {
  let service: Pipedrive

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Pipedrive(new ValidateJsAdapter(), pipedrivePayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    const { header: { app: { integration: { api_account, api_key } } } } = pipedrivePayloadMock

    it('should call method correctly', async() => {
      pipedrivePayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(pipedrivePayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        `https://${api_account}.pipedrive.com/v1/persons`,
        {
          email: 'subscriber@email.com',
          name: 'John Doe',
          phone: '19982867373',
          visible_to: 3
        },
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
          params: {
            api_token: api_key
          }
        }
      )
      expect(response).toBeUndefined()
    })
  })

  describe('bindRemoveContact', () => {
    const { header: { app: { integration: { api_account, api_key } } } } = pipedrivePayloadMock

    it('should call method correctly', async() => {
      pipedrivePayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindRemove = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindRemove).toBeCalledTimes(1)
      expect(spyOnBindRemove).toBeCalledWith(pipedrivePayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosDelete).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `https://${api_account}.pipedrive.com/v1/persons/search`,
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
          params: {
            api_token: api_key,
            fields: 'email',
            exact_match: true,
            term: 'subscriber@email.com'
          }
        }
      )
      expect(spyOnAxiosDelete).toBeCalledWith(
        `https://${api_account}.pipedrive.com/v1/persons/anypersonid`,
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
          params: {
            api_token: api_key,
          }
        }
      )
      expect(response).toBeUndefined()
    })

    it('should call method correctly with axios get null return', async() => {
      pipedrivePayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxiosGet = jest.spyOn(axios, 'get').mockImplementationOnce(async (url) => ({
        data: { data: { items: [] } }
      }))
      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindRemove = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindRemove).toBeCalledTimes(1)
      expect(spyOnBindRemove).toBeCalledWith(pipedrivePayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosDelete).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `https://${api_account}.pipedrive.com/v1/persons/search`,
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
          params: {
            api_token: api_key,
            fields: 'email',
            exact_match: true,
            term: 'subscriber@email.com'
          }
        }
      )
      expect(spyOnAxiosDelete).toBeCalledWith(
        `https://${api_account}.pipedrive.com/v1/persons/undefined`,
        {
          headers: {
            Accept: 'application/json',
            Authorization: expect.any(String)
          },
          params: {
            api_token: api_key,
          }
        }
      )
      expect(response).toBeUndefined()
    })
  })
})
