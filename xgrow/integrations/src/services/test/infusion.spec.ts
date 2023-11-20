import { Infusion } from '../infusion'
import { infusionPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  delete: jest.fn(() => ({ data: { any: 'anyres' } })),
  post: jest.fn(() => ({ data: { any: 'anyres' } })),
  put: jest.fn((url) => {
    return { data: { id: '1234', tag_ids: [777] } }
  })
}))

describe('Infusion Service', () => {
  let service: Infusion
  const { header: { app: { integration: { api_key } } } } = infusionPayloadMock
  const expectedAxiosHeader = {
    Accept: 'application/json',
    Authorization: `Bearer ${api_key}`
  }

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Infusion(new ValidateJsAdapter(), infusionPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContactTag', () => {
    it('should call method correctly and create contact tag', async() => {
      infusionPayloadMock.header.app.action = 'bindInsertContactTag'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContactTag')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(infusionPayloadMock)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://api.infusionsoft.com/crm/rest/v1/contacts',
        expect.objectContaining({
          given_name: 'John Doe',
          email_addresses: [{
            email: 'subscriber@email.com',
            field: 'EMAIL1'
          }],
          phone_numbers: [{
            number: '19982867373',
            field: 'PHONE1'
          }]
        }),
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://api.infusionsoft.com/crm/rest/v1/contacts/1234/tags',
        { tagIds: ['777'] },
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })

    it('shouldt apply tags to contact', async() => {
      infusionPayloadMock.header.app.action = 'bindInsertContactTag'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put').mockImplementationOnce(async() => ({}))
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContactTag')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(infusionPayloadMock)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://api.infusionsoft.com/crm/rest/v1/contacts',
        expect.objectContaining({
          given_name: 'John Doe',
          email_addresses: [{
            email: 'subscriber@email.com',
            field: 'EMAIL1'
          }],
          phone_numbers: [{
            number: '19982867373',
            field: 'PHONE1'
          }]
        }),
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledTimes(0)
      expect(response).toBeUndefined()
    })
  })

  describe('bindRemoveContactTag', () => {
    it('should call method correctly', async() => {
      infusionPayloadMock.header.app.action = 'bindRemoveContactTag'
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContactTag')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(infusionPayloadMock)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosDelete).toBeCalledTimes(1)
      expect(spyOnAxiosDelete).toBeCalledWith(
        'https://api.infusionsoft.com/crm/rest/v1/contacts/1234/tags?ids=777',
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })
  })
})
