import { Activecampaign } from '../activecampaign'
import { activecampaignPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'
import { Payload } from '../../job'

jest.mock('axios', () => ({
  get: jest.fn(() => ({ data: { contactTags: [{ tag: '777', id: '777' }] } })),
  delete: jest.fn(() => ({ data: { any: 'anyres' } })),
  post: jest.fn((url) => {
    switch (url) {
      case 'activecampaign.com/api/3/contact/sync':
        return { data: { contact: { id: '1111' } } }
      case 'activecampaign.com/api/3/contactLists':
        return { data: { contactList: { list: '2222' } } }
      default:
        return { data: { any: 'anydata' } }
    }
  }),

}))

describe('Activecampaign Service', () => {
  let service: Activecampaign
  const { header: { app: { integration: { api_key, api_webhook } } } } = activecampaignPayloadMock
  const expectedAxiosHeaders = {
    headers: {
      Accept: 'application/json',
      'Api-Token': api_key
    }
  }

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Activecampaign(new ValidateJsAdapter(), activecampaignPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    it('should call method correctly and create contact', async() => {
      activecampaignPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(activecampaignPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(3)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        `${api_webhook}/api/3/contact/sync`,
        {
          contact: {
            email: 'subscriber@email.com',
            firstName: 'John Doe',
            phone: '19982867373',
            fieldValues: [{
              field: 'anyid',
              value: 'url'
            }],
          }
        },
        expectedAxiosHeaders
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        `${api_webhook}/api/3/contactLists`,
        {
          contactList: {
            list: 1,
            contact: 1111,
            status: 1
          }
        },
        expectedAxiosHeaders
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        3,
        `${api_webhook}/api/3/contactTags`,
        {
          contactTag: {
            contact: 1111,
            tag: 777
          }
        },
        expectedAxiosHeaders
      )
      expect(response).toBeUndefined()
    })

    it('should call method correctly and create contact if axios updateList returns nothing', async() => {
      activecampaignPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosPost = jest.spyOn(axios, 'post').mockImplementation(
        async (url) => {
          switch (url) {
            case 'activecampaign.com/api/3/contact/sync':
              return { data: { contact: { id: '1111' } } }
            case 'activecampaign.com/api/3/contactLists':
              return {}
            default:
              return { data: { any: 'anydata' } }
          }
        })
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(activecampaignPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(3)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        `${api_webhook}/api/3/contact/sync`,
        {
          contact: {
            email: 'subscriber@email.com',
            firstName: 'John Doe',
            phone: '19982867373',
            fieldValues: [{
              field: 'anyid',
              value: 'url'
            }],
          }
        },
        expectedAxiosHeaders
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        `${api_webhook}/api/3/contactLists`,
        {
          contactList: {
            list: 1,
            contact: 1111,
            status: 1
          }
        },
        expectedAxiosHeaders
      )
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        3,
        `${api_webhook}/api/3/contactTags`,
        {
          contactTag: {
            contact: 1111,
            tag: 777
          }
        },
        expectedAxiosHeaders
      )
      expect(response).toBeUndefined()
    })

    it('should call method correctly and create contact without metadata', async() => {
      const payload: Payload = {
        ...activecampaignPayloadMock,
        header: {
          ...activecampaignPayloadMock.header,
          app: {
            ...activecampaignPayloadMock.header.app,
            metadata: undefined
          }
        }
      }
      payload.header.app.action = 'bindInsertContact'
      const service = new Activecampaign(new ValidateJsAdapter(), payload)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payload)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        `${api_webhook}/api/3/contact/sync`,
        {
          contact: {
            email: 'subscriber@email.com',
            firstName: 'John Doe',
            phone: '19982867373',
          }
        },
        expectedAxiosHeaders
      )
      expect(response).toBeUndefined()
    })

    it('should call method correctly and create contact without fieldValues', async() => {
      const payloadWithoutFieldValues: Payload = {
        ...activecampaignPayloadMock,
        payload: {
          data: {
            ...activecampaignPayloadMock.payload.data,
            transaction_origin: '',
            change_card_url: ''
          }
        }
      }
      const service = new Activecampaign(new ValidateJsAdapter(), payloadWithoutFieldValues)
      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payloadWithoutFieldValues)
      expect(spyOnAxiosPost).toBeCalledTimes(3)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
        `${api_webhook}/api/3/contact/sync`,
        {
          contact: {
            email: 'subscriber@email.com',
            firstName: 'John Doe',
            phone: '19982867373',
          }
        },
        expectedAxiosHeaders
      )
      expect(response).toBeUndefined()
    })

    it('should throw specific error if createOrUpdateContact resolves different than expected', async() => {
      activecampaignPayloadMock.header.app.action = 'bindInsertContact'
      const spyOnAxiosPost = jest.spyOn(axios, 'post').mockImplementationOnce(async () =>
        Promise.resolve({}))
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      await expect(service.process()).rejects.toThrowError('An error ocurred when try to create/update a contact')
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(activecampaignPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        1,
          `${api_webhook}/api/3/contact/sync`,
          {
            contact: {
              email: 'subscriber@email.com',
              firstName: 'John Doe',
              phone: '19982867373',
              fieldValues: [{
                field: 'anyid',
                value: 'url'
              }]
            }
          },
          expectedAxiosHeaders
      )
    })
  })

  describe('bindRemoveContact', () => {
    it('should call method correctly', async() => {
      activecampaignPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(activecampaignPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        `${api_webhook}/api/3/contactLists`,
        {
          contactList: {
            list: 1,
            contact: 1111,
            status: 2
          }
        },
        expectedAxiosHeaders
      )
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `${api_webhook}/api/3/contacts/1111/contactTags`,
        expectedAxiosHeaders
      )
      expect(spyOnAxiosDelete).toBeCalledTimes(1)
      expect(spyOnAxiosDelete).toBeCalledWith(
        `${api_webhook}/api/3/contactTags/777`,
        expectedAxiosHeaders
      )
      expect(response).toBeUndefined()
    })

    it('should call method correctly with axios getContactTags req unexpected response', async() => {
      activecampaignPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosGet = jest.spyOn(axios, 'get').mockImplementationOnce(async () => ({}))
      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(activecampaignPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(2)
      expect(spyOnAxiosPost).toHaveBeenNthCalledWith(
        2,
        `${api_webhook}/api/3/contactLists`,
        {
          contactList: {
            list: 1,
            contact: 1111,
            status: 2
          }
        },
        expectedAxiosHeaders
      )
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `${api_webhook}/api/3/contacts/1111/contactTags`,
        expectedAxiosHeaders
      )
      expect(spyOnAxiosDelete).toBeCalledTimes(0)
      expect(response).toBeUndefined()
    })

    it('should call method correctly without metadata', async() => {
      const payload: Payload = {
        ...activecampaignPayloadMock,
        header: {
          ...activecampaignPayloadMock.header,
          app: {
            ...activecampaignPayloadMock.header.app,
            metadata: undefined
          }
        }
      }
      payload.header.app.action = 'bindRemoveContact'
      const service = new Activecampaign(new ValidateJsAdapter(), payload)

      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnAxiosGet = jest.spyOn(axios, 'get')
      const spyOnAxiosDelete = jest.spyOn(axios, 'delete')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(payload)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledTimes(1)
      expect(spyOnAxiosGet).toBeCalledWith(
        `${api_webhook}/api/3/contacts/1111/contactTags`,
        expectedAxiosHeaders
      )
      expect(spyOnAxiosDelete).toBeCalledTimes(0)
      expect(response).toBeUndefined()
    })
  })
})
