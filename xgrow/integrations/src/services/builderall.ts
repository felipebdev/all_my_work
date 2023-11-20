import axios from 'axios'
import FormData from 'form-data'
import { IInsertContact, IRemoveContact } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'

const BUILDER_ALL_URL = 'https://member.mailingboss.com/integration/index.php'

const builderAllApi = axios.create({
  baseURL: BUILDER_ALL_URL,
})

enum IBuilderAllPaths {
  INSERT_SUBSCRIBER = '/lists/subscribers/create',
  DELETE_SUBSCRIBER = '/lists/subscribers/delete',
  SEARCH_SUBSCRIBER_BY_EMAIL = '/lists/subscribers/search-by-email',
}

interface Response {
  status: 'success' | 'error'
  data?: ISubscriber
  error?: string
  successMessage?: string
}

interface ISubscriber {
  subscriber_uid: string
  email: string
  taginternals?: string
  status: string
}

// interface ICreateSubscriberPayload {
//   email: string
//   taginternals?: string
//   list_uid: string
// }

// interface ISearchSubscriberPayload {
//   email: string
//   list_uid: string
// }

// interface IRemoveSubscriberPayload {
//   subscriber_uid: string
//   list_uid: string
// }

interface IRequestData { apiKey: string, listUid: string }

export class Builderall extends BaseService implements IInsertContact, IRemoveContact {
  // private static readonly HTTP_ACCEPT = 'application/x-www-form-urlencoded'
  private static readonly HTTP_ACCEPT = 'multipart/form-data'
  protected validateSchema = {
    'header.app.action': value => (!!value && ['bindInsertContact', 'bindRemoveContact'].includes(value)),
    'header.app.metadata.list': value => !!value,
    'header.app.integration.api_key': value => !!value,
    'payload.data.subscriber_email': value => !!value,
  }

  public async bindInsertContact(payload: Payload): Promise<void> {
    const { apiKey, listUid } = this.getRequestData(payload)
    const { subscriber_email: email } = payload.payload.data
    const subscriberData = new FormData()

    subscriberData.append('list_uid', listUid)
    subscriberData.append('email', email)
    // subscriberData.append('taginternals', taginternals || '')

    await this.createSubscriber(apiKey, subscriberData)
  }

  public async bindRemoveContact(payload: Payload): Promise<void> {
    const { apiKey, listUid } = this.getRequestData(payload)
    const { subscriber_email: email } = payload.payload.data

    const findSubscriberData = new FormData()

    findSubscriberData.append('list_uid', listUid)
    findSubscriberData.append('email', email)

    const { data: subscriber } = await this.searchSubscriber(apiKey, findSubscriberData)

    if (!subscriber) {
      throw new Error("Subscriber doesn't exist")
    }

    const removeSubscriberData = new FormData()

    removeSubscriberData.append('list_uid', listUid)
    removeSubscriberData.append('subscriber_uid', subscriber.subscriber_uid)

    await this.removeSubscriber(apiKey, removeSubscriberData)
  }

  private getRequestData(payload: Payload): IRequestData {
    const {
      header: {
        app: {
          integration: {
            api_key: apiKey,
          },
          metadata: {
            list: listUid,
          },
        }
      },
    } = payload

    return {
      apiKey,
      listUid,
    }
  }

  private async createSubscriber(apiToken: string, payload: FormData): Promise<Response> {
    const response = await builderAllApi.post<Response>(`${IBuilderAllPaths.INSERT_SUBSCRIBER}/${apiToken}`, payload, {
      headers: payload.getHeaders()
    })
    if (response?.data?.status === 'error') {
      throw new Error(response.data.error)
    }

    if (response?.status !== 200) {
      throw new Error('Error on create subscriber')
    }

    return response.data
  }

  private async searchSubscriber(apiToken: string, payload: FormData): Promise<Response> {
    const response = await builderAllApi.post<Response>(`${IBuilderAllPaths.SEARCH_SUBSCRIBER_BY_EMAIL}/${apiToken}`, payload, {
      headers: payload.getHeaders()
    })
    if (response?.data?.status === 'error') {
      throw new Error(response.data.error)
    }

    if (response?.status !== 200) {
      throw new Error('Error on search subscriber')
    }

    return response.data
  }

  private async removeSubscriber(apiToken: string, payload: FormData): Promise<Response> {
    const response = await builderAllApi.post<Response>(`${IBuilderAllPaths.DELETE_SUBSCRIBER}/${apiToken}`, payload, {
      headers: payload.getHeaders()
    })
    if (response?.data?.status === 'error') {
      throw new Error(response.data.error)
    }

    if (response?.status !== 200) {
      throw new Error('Error on remove subscriber')
    }

    return response.data
  }
}
