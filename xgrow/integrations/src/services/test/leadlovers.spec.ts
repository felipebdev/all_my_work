import { Leadlovers } from '../leadlovers'
import { leadloversPayloadMock } from './services.mock'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import axios from 'axios'
// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ValidateJsAdapter } from '../../adapters/validatejs-adapter'

jest.mock('axios', () => ({
  put: jest.fn((url) => {
    switch (url) {
      default:
        return { data: { code: 'anycode' } }
    }
  }),
  delete: jest.fn((url) => {
    switch (url) {
      default:
        return { any: 'anyresfromdelete' }
    }
  }),
}))

describe('Leadlovers Service', () => {
  let service: Leadlovers

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  describe('service initialization', () => {
    it('should be defined', () => {
      service = new Leadlovers(new ValidateJsAdapter(), leadloversPayloadMock)
      expect(service).toBeDefined()
    })
  })

  describe('bindInsertContact', () => {
    const { header: { app: { integration: { api_key } } } } = leadloversPayloadMock

    it('should call bindInsertContact correctly', async() => {
      leadloversPayloadMock.header.app.action = 'bindInsertContact'

      const spyOnAxios = jest.spyOn(axios, 'put')
      const spyOnBindInsert = jest.spyOn(service, 'bindInsertContact')
      const response = await service.process()
      expect(spyOnBindInsert).toBeCalledTimes(1)
      expect(spyOnBindInsert).toBeCalledWith(leadloversPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(
        'http://llapi.leadlovers.com/webapi/Lead',
        {
          MachineCode: '1234',
          EmailSequenceCode: '12345',
          SequenceLevelCode: '123456',
          Email: 'contact@email.com',
          Name: 'anyName',
          Phone: '19982867777',
          City: 'Indaiatuba',
          State: 'SP',
          Tags: 'any, any2'
        },
        expect.objectContaining({ params: { token: api_key } })
      )
      expect(response).toBeUndefined()
    })
  })

  describe('bindRemoveContact', () => {
    const { header: { app: { integration: { api_key } } } } = leadloversPayloadMock

    it('should call bindRemoveContact correctly', async() => {
      leadloversPayloadMock.header.app.action = 'bindRemoveContact'

      const spyOnAxios = jest.spyOn(axios, 'delete')
      const spyOnBindRemove = jest.spyOn(service, 'bindRemoveContact')
      const response = await service.process()
      expect(spyOnBindRemove).toBeCalledTimes(1)
      expect(spyOnBindRemove).toBeCalledWith(leadloversPayloadMock)
      expect(spyOnAxios).toBeCalledTimes(1)
      expect(spyOnAxios).toBeCalledWith(
        'http://llapi.leadlovers.com/webapi/Lead/Funnel',
        expect.objectContaining({
          params: {
            token: api_key,
            machineCode: '1234',
            sequenceCode: '12345',
            email: 'contact@email.com'
          }
        })
      )
      expect(response).toBeUndefined()
    })
  })
})
