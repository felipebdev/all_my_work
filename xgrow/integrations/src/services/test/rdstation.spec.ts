import { Rdstation } from '../rdstation'
import { rdstationPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  post: jest.fn(() => ({ data: { any: 'anyres' } })),
}))

describe('Rdstation Service', () => {
  let service: Rdstation

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Rdstation(new ValidateJsAdapter(), rdstationPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    it('should call method correctly', async() => {
      const { header: { app: { integration: { api_key } } } } = rdstationPayloadMock
      rdstationPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(rdstationPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://api.rd.services/platform/conversions/',
        expect.objectContaining({
          payload: expect.objectContaining({
            email: 'subscriber@email.com',
            name: 'John Doe',
            conversion_identifier: 'anyevent',
            legal_bases: [
              expect.objectContaining({
                status: 'granted'
              })
            ],
            tags: ['tag1, tag2']
          }),
        }),
        { headers: { 'Content-Type': 'application/json' }, params: { api_key } }
      )
      expect(response).toBeUndefined()
    })

    it('should call method correctly without metadata', async() => {
      const { header: { app: { integration: { api_key } } } } = rdstationPayloadMock
      rdstationPayloadMock.header.app.action = 'bindInsertContact'
      rdstationPayloadMock.header.app.metadata = undefined

      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(rdstationPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://api.rd.services/platform/conversions/',
        expect.objectContaining({
          payload: expect.objectContaining({
            email: 'subscriber@email.com',
            name: 'John Doe',
            conversion_identifier: 'anyevent',
            legal_bases: [
              expect.objectContaining({
                status: 'granted'
              })
            ],
            tags: []
          }),
        }),
        { headers: { 'Content-Type': 'application/json' }, params: { api_key } }
      )
      expect(response).toBeUndefined()
    })

    it('should throw dinamic error if axios req fails', async () => {
      console.log = jest.fn()
      const { header: { app: { integration: { api_key } } } } = rdstationPayloadMock
      rdstationPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxiosPost = jest.spyOn(axios, 'post').mockImplementationOnce(() => {
        // eslint-disable-next-line @typescript-eslint/no-throw-literal
        throw { response: { data: 'anyerror' } }
      })
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(rdstationPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://api.rd.services/platform/conversions/',
        expect.objectContaining({
          payload: expect.objectContaining({
            email: 'subscriber@email.com',
            name: 'John Doe',
            conversion_identifier: 'anyevent',
            legal_bases: [
              expect.objectContaining({
                status: 'granted'
              })
            ]
          }),
        }),
        { headers: { 'Content-Type': 'application/json' }, params: { api_key } }
      )
      expect(response).toBeUndefined()
      expect(console.log).toBeCalledWith('Error on RdStation createOrUpdate', 'anyerror')
    })

    it('should console.log undefined if axios req fails with undefined error response', async () => {
      console.log = jest.fn()
      const { header: { app: { integration: { api_key } } } } = rdstationPayloadMock
      rdstationPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxiosPost = jest.spyOn(axios, 'post').mockImplementationOnce(() => {
        // eslint-disable-next-line @typescript-eslint/no-throw-literal
        throw undefined
      })
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(rdstationPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://api.rd.services/platform/conversions/',
        expect.objectContaining({
          payload: expect.objectContaining({
            email: 'subscriber@email.com',
            name: 'John Doe',
            conversion_identifier: 'anyevent',
            legal_bases: [
              expect.objectContaining({
                status: 'granted'
              })
            ]
          }),
        }),
        { headers: { 'Content-Type': 'application/json' }, params: { api_key } }
      )
      expect(response).toBeUndefined()
      expect(console.log).toBeCalledWith('Error on RdStation createOrUpdate', undefined)
    })
  })

  describe('bindRemoveContact', () => {
    it('should call method correctly', async() => {
      const { header: { app: { integration: { api_key } } } } = rdstationPayloadMock
      rdstationPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxiosPost = jest.spyOn(axios, 'post')
      const spyOnBindInsert = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(rdstationPayloadMock)
      expect(spyOnAxiosPost).toBeCalledTimes(1)
      expect(spyOnAxiosPost).toBeCalledWith(
        'https://api.rd.services/platform/conversions/',
        expect.objectContaining({
          payload: expect.objectContaining({
            email: 'subscriber@email.com',
            conversion_identifier: 'anyevent',
            legal_bases: [
              expect.objectContaining({
                status: 'declined'
              })
            ]
          }),
        }),
        { headers: { 'Content-Type': 'application/json' }, params: { api_key } }
      )
      expect(response).toBeUndefined()
    })
  })
})
