import { Mailchimp } from '../mailchimp'
import { mailchimpPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import crypto from 'crypto'
import { Payload } from '../../job'

jest.mock('axios', () => ({
  put: jest.fn(() => ({ data: { id: '1234',list_id: '1', tags: [{ id: '777', name: 'anyname' }] } })),
  post: jest.fn(() => ({ data: { any: 'any-res' } }))
}))

jest.mock('crypto', () => ({
  createHash: jest.fn().mockReturnThis(),
  update: jest.fn().mockReturnThis(),
  digest: jest.fn(() => 'anyhex'),
  randomBytes: jest.requireActual('crypto').randomBytes
}))

describe('Mailchimp Service', () => {
  let service: Mailchimp
  const { header: { app: { integration: { api_key } } } } = mailchimpPayloadMock

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
      service = new Mailchimp(new ValidateJsAdapter(), mailchimpPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    it('should assert axios calls ', async() => {
      mailchimpPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(mailchimpPayloadMock)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex',
        {
          email_address: 'subscriber@email.com',
          status: 'subscribed',
          status_if_new: 'subscribed',
          merge_fields: {
            FNAME: 'John',
            LNAME: 'Doe'
          }
        },
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex/tags',
        {
          tags: [{ name: '777', status: 'active' }]
        },
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })

    it('should assert axios calls with axios createContact res contactTags as empty array', async() => {
      mailchimpPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put').mockImplementationOnce(async () => ({ data: { contact: { id: '1234',list_id: '1' } } }))
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(mailchimpPayloadMock)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex',
        {
          email_address: 'subscriber@email.com',
          status: 'subscribed',
          status_if_new: 'subscribed',
          merge_fields: {
            FNAME: 'John',
            LNAME: 'Doe'
          }
        },
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex/tags',
        {
          tags: [{ name: '777', status: 'active' }]
        },
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })

    it('should assert axios calls with firstNameOnly', async() => {
      const payload: Payload = {
        ...mailchimpPayloadMock,
      }
      payload.header.app.action = 'bindInsertContact'
      payload.payload.data.subscriber_name = 'John'
      const service = new Mailchimp(new ValidateJsAdapter(), payload)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payload)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex',
        {
          email_address: 'subscriber@email.com',
          status: 'subscribed',
          status_if_new: 'subscribed',
          merge_fields: {
            FNAME: 'John',
            LNAME: ''
          }
        },
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex/tags',
        {
          tags: [{ name: '777', status: 'active' }]
        },
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })

    it('should assert axios calls without tags', async() => {
      const payload: Payload = {
        ...mailchimpPayloadMock,
        header: {
          ...mailchimpPayloadMock.header,
          app: {
            ...mailchimpPayloadMock.header.app,
            metadata: {
              ...mailchimpPayloadMock.header.app.metadata,
              tags: undefined
            }
          }
        }
      }
      payload.payload.data.subscriber_name = 'John Doe'
      payload.header.app.action = 'bindInsertContact'
      const service = new Mailchimp(new ValidateJsAdapter(), payload)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payload)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex',
        {
          email_address: 'subscriber@email.com',
          status: 'subscribed',
          status_if_new: 'subscribed',
          merge_fields: {
            FNAME: 'John',
            LNAME: 'Doe'
          }
        },
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex/tags',
        {
          tags: []
        },
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })
  })

  describe('bindRemoveContact', () => {
    it('should assert axios calls ', async() => {
      mailchimpPayloadMock.header.app.action = 'bindRemoveContact'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(mailchimpPayloadMock)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex',
        { status: 'unsubscribed' },
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex/tags',
        {
          tags: [{ name: '777', status: 'inactive' }]
        },
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })

    it('should assert axios calls without tags', async() => {
      const payload: Payload = {
        ...mailchimpPayloadMock,
        header: {
          ...mailchimpPayloadMock.header,
          app: {
            ...mailchimpPayloadMock.header.app,
            metadata: {
              ...mailchimpPayloadMock.header.app.metadata,
              tags: undefined
            }
          }
        }
      }
      payload.header.app.action = 'bindRemoveContact'
      const service = new Mailchimp(new ValidateJsAdapter(), payload)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosPut = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payload)
      expect(spyOnAxiosPut).toBeCalledTimes(1)
      expect(spyOnAxiosPut).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex',
        { status: 'unsubscribed' },
        { headers: expectedAxiosHeader }
      )
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://mailchimpdatacentercode.api.mailchimp.com/3.0/lists/1/members/anyhex/tags',
        {
          tags: []
        },
        { headers: expectedAxiosHeader }
      )
      expect(response).toBeUndefined()
    })
  })
})
