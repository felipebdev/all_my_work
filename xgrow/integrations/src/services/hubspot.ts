import axios from 'axios'
import { IInsertContact, IRemoveContact } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'

enum ListAction {
  ADD = 'add',
  REMOVE = 'remove'
}

interface Request {
  url: string
  headers: {
    Accept: string
  }
  params: {
    hapikey: string
  }
}

interface Response {
  contactId: number
}

interface CreateOrUpdateContactBody {
  properties: Array<{property: string, value: string}>
}

export class Hubspot extends BaseService implements IInsertContact, IRemoveContact {
  private static readonly HTTP_ACCEPT = 'application/json'
  private static readonly BASE_URL = 'https://api.hubapi.com/contacts/v1'
  private static API_KEY = ''

  protected validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindInsertContact', 'bindRemoveContact'].includes(value)),
    'header.app.integration.api_key': (value: string) => !!value,
    'payload.data.subscriber_email': (value: string) => !!value,
    'payload.data.subscriber_name': (value: string) => !!value
  }

  private request (payload: Payload): Request {
    const { api_key } = payload.header.app.integration
    Hubspot.API_KEY = api_key

    const request: Request = {
      url: Hubspot.BASE_URL,
      headers: {
        Accept: Hubspot.HTTP_ACCEPT
      },
      params: {
        hapikey: Hubspot.API_KEY
      }
    }

    return request
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const listId = payload.header.app.metadata?.list
    const { subscriber_email: contactEmail } = payload.payload.data

    const createOrUpdateContactBody: CreateOrUpdateContactBody = this.buildCreateOrUpdateContactBody(payload)

    const { contactId } = await this.createOrUpdateContact(request, contactEmail, createOrUpdateContactBody)

    if (listId) {
      await this.addOrRemoveContactFromList(request, listId, contactId, ListAction.ADD)
    }
  }

  public async bindRemoveContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { subscriber_email: contactEmail } = payload.payload.data

    const { contactId } = await this.getContactIdByEmail(request, contactEmail)

    await this.removeContact(request, contactId)
  }

  private async createOrUpdateContact (
    request: Request,
    contactEmail: string,
    createOrUpdateContactBody: CreateOrUpdateContactBody
  ): Promise<Response> {
    const { headers, params } = request

    const { data: contact } = await axios.post(
      `${request.url}/contact/createOrUpdate/email/${contactEmail}`,
      { ...createOrUpdateContactBody },
      { headers, params }
    )

    return { contactId: contact.vid }
  }

  private async removeContact (
    request: Request,
    contactId: number
  ): Promise<void> {
    const { headers, params } = request

    await axios.delete(
      `${request.url}/contact/vid/${contactId}`,
      { headers, params }
    )
  }

  private async getContactIdByEmail (
    request: Request,
    contactEmail: string
  ): Promise<Response> {
    const { headers, params } = request

    const { data: contact } = await axios.get(
      `${request.url}/contact/email/${contactEmail}/profile`,
      { headers, params }
    )

    return { contactId: contact.vid }
  }

  private async addOrRemoveContactFromList (
    request: Request,
    listId: number,
    contactId: number,
    listAction: ListAction
  ): Promise<void> {
    const { headers, params } = request

    await axios.post(
      `${request.url}/lists/${listId}/${listAction}`,
      { vids: [contactId] },
      { headers, params }
    )
  }

  private buildCreateOrUpdateContactBody (payload: Payload): CreateOrUpdateContactBody {
    const {
      subscriber_name: fullName,
      subscriber_phone: phone,
      subscriber_street: street,
      subscriber_number: streetNumber,
      subscriber_city: city,
      subscriber_state: state,
      subscriber_zipcode: zip
    } = payload.payload.data

    const splitedName = fullName.split(' ')

    const createOrUpdateContactBody: CreateOrUpdateContactBody = {
      properties: [
        {
          property: 'firstname',
          value: splitedName[0]
        },
        {
          property: 'phone',
          value: phone
        },
        {
          property: 'address',
          value: `${street}, ${streetNumber}`
        },
        {
          property: 'city',
          value: city
        },
        {
          property: 'state',
          value: state
        },
        {
          property: 'zip',
          value: zip
        }
      ]
    }

    if (splitedName[splitedName.length - 1] !== splitedName[0]) {
      createOrUpdateContactBody.properties.push({
        property: 'lastname',
        value: splitedName[splitedName.length - 1]
      })
    }

    return createOrUpdateContactBody
  }
}
