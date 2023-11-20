/* eslint-disable import/first */

const logMock = {
  create: jest.fn(),
  LogStatus: {
    FAILED: 'failed',
    SUCCESS: 'success'
  }
}

import { Payload } from '@app/job'
/* eslint-disable @typescript-eslint/no-unused-vars */
import Log, { LogStatus } from '../../collections/log'
import { LogFactory } from '../log.factory'
import mongoose from 'mongoose'
import { AxiosResponse } from 'axios'

jest.mock('../../collections/log', () => logMock)

jest.mock('mongoose', () => ({
  connection: {
    readyState: 1
  }
}))

const payloadMock: Payload = {
  header: {
    app: {
      action: 'anyaction',
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        api_key: 'anyapykey',
        id: 434,
        type: 'anyvaluetype',
        api_webhook: 'anyweb.com'
      },
      planIds: [
        1,
        2
      ],
      platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
    },
    date: '2022-12-30 08:54:23'
  },
  payload: {
    data: {
      payment_date: '2022-12-30',
      payment_order_code: 'anycode'
    }
  }
}

const mockRequest = {
  url: 'anyurl',
  method: 'anymethod',
  headers: 'anyheaders',
} as unknown as Request

const mockResponse = {
  status: 301,
  statusText: 'anystatustext',
  data: { any: 'anydata' },
  headers: 'anyheaders',
  config: {
    url: 'anyurl'
  }
} as unknown as AxiosResponse

describe('LogFactory', () => {
  let factory: LogFactory

  beforeEach(() => {
    jest.clearAllMocks()
    jest.clearAllTimers()
  })

  it('should initalize class', () => {
    factory = new LogFactory()
    expect(factory).toBeDefined()
  })

  // it('should correctly call Log.create()', async() => {
  //   await factory.createLog(
  //     'anyjobId',
  //     payloadMock,
  //     mockRequest,
  //     mockResponse
  //   )
  //   expect(logMock.create).toBeCalledTimes(1)
  //   expect(logMock.create).toBeCalledWith({
  //     jobId: 'anyjobId',
  //     service: 'anyvaluetype',
  //     status: LogStatus.FAILED,
  //     metadata: {
  //       action_id: 794,
  //       app_id: 434,
  //       platform_id: '89d6084b-99ae-481c-8646-05c99c98b469',
  //       event: 'anyevent'
  //     },
  //     request: {
  //       url: 'anyurl',
  //       method: 'anymethod',
  //       key: 'anyapykey',
  //       headers: 'anyheaders',
  //       payload: {
  //         payment_date: '2022-12-30',
  //         payment_order_code: 'anycode'
  //       }
  //     },
  //     response: {
  //       code: 301,
  //       message: 'anystatustext',
  //       payload: { any: 'anydata' }
  //     }
  //   })
  // })

  it('should correctly call Log.handleResponse()', () => {
    const resonse = factory.handleResponse(mockResponse)
    expect(resonse).toStrictEqual({
      status: 301,
      statusText: 'anystatustext',
      data: { any: 'anydata' },
      headers: 'anyheaders',
      config: {
        url: 'anyurl'
      }
    })
  })

  it('should call Log.create() with log status success and default response and request params', async() => {
    await factory.createLog(
      'anyjobId',
      payloadMock,
      undefined,
      undefined
    )
    expect(logMock.create).toBeCalledTimes(1)
    expect(logMock.create).toBeCalledWith({
      jobId: 'anyjobId',
      service: 'anyvaluetype',
      status: LogStatus.SUCCESS,
      metadata: {
        action_id: 794,
        app_id: 434,
        platform_id: '89d6084b-99ae-481c-8646-05c99c98b469',
        event: 'anyevent'
      },
      request: {
        url: 'anyweb.com',
        method: 'get',
        key: 'anyapykey',
        headers: { },
        payload: {
          payment_date: '2022-12-30',
          payment_order_code: 'anycode'
        }
      },
      response: {
        code: undefined,
        message: undefined,
        payload: {}
      }
    })
  })

  it('should call Log.handleResponse() with default params', () => {
    const response = factory.handleResponse(undefined)
    expect(response).toStrictEqual({
      status: undefined,
      statusText: undefined,
      data: undefined,
      headers: undefined,
      config: undefined
    })
  })
})
