import { Hubspot } from '../hubspot'
import { hubspotPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  get: jest.fn(() => ({ data: { vid: '1234' } })),
  delete: jest.fn(() => ({ data: { any: 'anyres' } })),
  post: jest.fn((url) => ({ data: { vid: '1234' } }))
}))

describe('Hubspot Service', () => {
  let service: Hubspot
  const { header: { app: { integration: { api_key } } } } = hubspotPayloadMock
  const expectedAxiosParams = {
    url: 'https://api.hubapi.com/contacts/v1',
    headers: {
      Accept: 'application/json'
    },
    params: {
      hapikey: api_key
    }
  }

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Hubspot(new ValidateJsAdapter(), hubspotPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    const { url, headers, params } = expectedAxiosParams
    const expectedPostPayload = {
      properties: [
        {
          property: 'firstname',
          value: 'John'
        },
        {
          property: 'phone',
          value: '19982867373'
        },
        {
          property: 'address',
          value: 'anystreet, 123'
        },
        {
          property: 'city',
          value: 'Indaiatuba'
        },
        {
          property: 'state',
          value: 'SP'
        },
        {
          property: 'zip',
          value: '13340501'
        },
        {
          property: 'lastname',
          value: 'Doe'
        }
      ]
    }
    it('should call method correctly and create contact', async() => {
      hubspotPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(hubspotPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        `${url}/contact/createOrUpdate/email/subscriber@email.com`,
        expectedPostPayload,
        { headers, params }
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        `${url}/lists/1/add`,
        { vids: ['1234'] },
        { headers, params }
      )
      expect(response).toBeUndefined()
    })

    it('should call method correctly and create contact without add or removing from list', async() => {
      hubspotPayloadMock.header.app.action = 'bindInsertContact'
      hubspotPayloadMock.header.app.metadata = undefined
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(hubspotPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(response).toBeUndefined()
    })
  })

  describe('bindRemoveContact', () => {
    const { url, headers, params } = expectedAxiosParams

    it('should call method correctly', async() => {
      hubspotPayloadMock.header.app.action = 'bindRemoveContact'
      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(hubspotPayloadMock)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `${url}/contact/email/subscriber@email.com/profile`,
        { headers, params }
      )
      expect(spyOnAxiosDelete).toBeCalledTimes(1)
      expect(spyOnAxiosDelete).toBeCalledWith(
        `${url}/contact/vid/1234`,
        { headers, params }
      )
      expect(response).toBeUndefined()
    })
  })
})
