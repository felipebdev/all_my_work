import { AxiosResponse } from 'axios'
import mongoose from 'mongoose'
import Log, { LogStatus } from '../collections/log'
import { Payload } from '../../../job'

export class LogFactory {
  public async createLog (jobId: string, payload: Payload, request: Request, response: AxiosResponse): Promise<void> {
    const { type, api_webhook, api_key } = payload.header.app.integration
    const { platform_id, event, app_id, id: actionId } = payload.header.app

    if (mongoose.connection.readyState === 1) {
      await Log.create({
        jobId,
        service: type,
        status: response?.status >= 300 ? LogStatus.FAILED : LogStatus.SUCCESS,
        metadata: {
          action_id: actionId,
          app_id,
          platform_id,
          event
        },
        request: {
          url: request?.url || api_webhook,
          method: request?.method || 'get',
          key: api_key,
          headers: request?.headers || {},
          payload: payload.payload.data
        },
        response: {
          code: response?.status,
          message: response?.statusText,
          payload: response?.data || {}
        }
      })
    }
  }

  public handleResponse (response: any): any {
    return {
      status: response?.status,
      statusText: response?.statusText,
      data: response?.data ?? response?.config?.data,
      headers: response?.headers ?? response?.config?.headers,
      config: response?.config
    }
  }
}
