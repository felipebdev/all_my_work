import axios from 'axios'
import { IInsertContact, IRemoveContact } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'

enum Status {
  GRANTED = 'granted',
  DECLINED = 'declined'
}

enum Type {
  PRE_EXISTENT_CONTRACT = 'pre_existent_contract',
  CONSENT = 'consent',
  LEGITIMATE_INTEREST = 'legitimate_interest',
  JUDICIAL_PROCESS = 'judicial_process',
  VITAL_INTEREST = 'vital_interest',
  PUBLIC_INTETEST = 'public_interest'
}

interface Request {
  url: string
  headers: {
    'Content-Type'?: string
  }
  params: {
    api_key: string
  }
}
interface ContactBody {
  conversion_identifier: string
  email: string
  name?: string
  city?: string
  state?: string
  country?: string
  personal_phone?: string
  tags?: string[]
  legal_bases?: Array<{
    category: string
    type: Type
    status: Status
  }>
}

/**
 * @see https://developers.rdstation.com/pt-BR/overview
 */
export class Rdstation extends BaseService implements IInsertContact, IRemoveContact {
  private static readonly CONTENT_TYPE = 'application/json'
  private static readonly BASE_URL = 'https://api.rd.services/platform/conversions'

  protected validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindInsertContact', 'bindRemoveContact'].includes(value)),
    'header.app.integration.api_key': (value: string) => !!value,
    'payload.data.subscriber_email': (value: string) => !!value
  }

  private request (payload: Payload): Request {
    const { api_key } = payload.header.app.integration

    const request: Request = {
      url: Rdstation.BASE_URL,
      headers: {
        'Content-Type': Rdstation.CONTENT_TYPE
      },
      params: {
        api_key
      }
    }

    return request
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { event } = payload.header.app

    const {
      subscriber_email,
      subscriber_name,
      subscriber_city,
      subscriber_state,
      subscriber_country,
      subscriber_phone
    } = payload.payload.data

    const contactBody: ContactBody = {
      conversion_identifier: event,
      email: subscriber_email,
      name: subscriber_name,
      city: subscriber_city,
      state: subscriber_state,
      country: subscriber_country,
      personal_phone: subscriber_phone,
      tags: payload.header.app.metadata?.tags || [],
      legal_bases: [
        {
          category: 'communications',
          type: Type.CONSENT,
          status: Status.GRANTED
        }
      ]
    }

    await this.createOrUpdateContact(request, contactBody)
  }

  public async bindRemoveContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { subscriber_email } = payload.payload.data
    const { event } = payload.header.app

    const contactBody: ContactBody = {
      conversion_identifier: event,
      email: subscriber_email,
      legal_bases: [
        {
          category: 'communications',
          type: Type.CONSENT,
          status: Status.DECLINED
        }
      ]
    }

    await this.createOrUpdateContact(request, contactBody)
  }

  /**
   * @see https://developers.rdstation.com/pt-BR/reference/contacts#methodPatchDetails
  */
  private async createOrUpdateContact (
    request: Request,
    contactBody: ContactBody
  ): Promise<void> {
    const { headers, params } = request

    try {
      await axios.post(
        `${request.url}/`,
        {
          event_type: 'CONVERSION',
          event_family: 'CDP',
          payload: contactBody
        },
        { headers, params }
      )
    } catch (err) {
      console.log('Error on RdStation createOrUpdate', err?.response?.data)
    }
  }
}
