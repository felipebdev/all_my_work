import axios from 'axios'
import { IInsertContact, IRemoveContact } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'

interface Request {
  url: string
  headers: {
    Accept?: string
    Authorization: string
  }
}

interface Response {
  personId: number
}

enum PersonVisibleTo {
  PRIVATE = 1,
  SHARED = 3
}

interface CreatePersonBody {
  name: string
  email: string[]
  phone: string[]
  visible_to: PersonVisibleTo
}

export class Pipedrive extends BaseService implements IInsertContact, IRemoveContact {
  private static readonly HTTP_ACCEPT = 'application/json'
  private static API_KEY = ''

  validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindInsertContact', 'bindRemoveContact'].includes(value)),
    'header.app.integration.api_key': (value: string) => !!value,
    'header.app.integration.api_account': (value: string) => !!value,
    'payload.data.subscriber_email': (value: string) => !!value
  }

  private request (payload: Payload): Request {
    const { api_key, api_account } = payload.header.app.integration
    Pipedrive.API_KEY = api_key

    const request: Request = {
      url: `https://${api_account}.pipedrive.com/v1`,
      headers: {
        Accept: Pipedrive.HTTP_ACCEPT,
        Authorization: `Bearer ${api_key}`
      }
    }

    return request
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const {
      subscriber_email: email,
      subscriber_name: name,
      subscriber_phone: phone
    } = payload.payload.data

    const createPersonBody: CreatePersonBody = {
      email,
      name,
      phone,
      visible_to: PersonVisibleTo.SHARED
    }

    await this.createPerson(request, createPersonBody)
  }

  public async bindRemoveContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { subscriber_email: email } = payload.payload.data

    const { personId } = await this.getPersonByEmail(request, email)

    await this.removePerson(request, personId)
  }

  private async getPersonByEmail (request: Request, email: string): Promise<Response> {
    const { data: { data: { items: persons } } } = await axios.get(
      `${request.url}/persons/search`,
      {
        headers: request.headers,
        params: {
          api_token: Pipedrive.API_KEY,
          fields: 'email',
          exact_match: true,
          term: email
        }
      }
    )

    const person = persons[0]?.item ? persons[0].item : null

    return { personId: person?.id }
  }

  private async createPerson (
    request: Request,
    createPersonBody: CreatePersonBody
  ): Promise<Response> {
    const { data: person } = await axios.post(
      `${request.url}/persons`,
      { ...createPersonBody },
      {
        headers: request.headers,
        params: {
          api_token: Pipedrive.API_KEY
        }
      }
    )

    return { personId: person.id }
  }

  private async removePerson (
    request: Request,
    personId: number
  ): Promise<void> {
    await axios.delete(
      `${request.url}/persons/${personId}`,
      {
        headers: request.headers,
        params: {
          api_token: Pipedrive.API_KEY
        }
      }
    )
  }
}
