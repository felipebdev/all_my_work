import { IInsertContact, IRemoveContact } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'
import axios from 'axios'

interface Request {
  url: string
  headers: {
    Accept?: string
    Authorization: string
  }
}

interface Response {
  contactId: string
}

interface CreateOrUpdateContactBody {
  firstname: string
  lastname: string
  email: string
  phone: string
}

export class Mautic extends BaseService implements IInsertContact, IRemoveContact {
  private static readonly HTTP_ACCEPT = 'application/json'

  validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindInsertContact', 'bindRemoveContact'].includes(value)),
    'header.app.integration.api_key': (value: string) => !!value,
    'header.app.integration.api_account': (value: string) => !!value,
    'payload.data.subscriber_email': (value: string) => !!value
  }

  private request (payload: Payload): Request {
    const { api_account, api_key, api_webhook } = payload.header.app.integration

    const token = Buffer.from(`${api_account}:${api_key}`).toString('base64')

    const request: Request = {
      url: `${api_webhook}/api`,
      headers: {
        Accept: Mautic.HTTP_ACCEPT,
        Authorization: `Basic ${token}`
      }
    }

    return request
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { subscriber_email, subscriber_name, subscriber_phone } = payload.payload.data

    const { list: listId } = payload.header.app.metadata

    const createOrUpdateContactBody: CreateOrUpdateContactBody = {
      email: subscriber_email,
      firstname: subscriber_name.split(' ')[0],
      lastname: subscriber_name.split(' ')[1],
      phone: subscriber_phone
    }

    const { contactId: foundedContactId } = await this.getContactIdByEmail(request, subscriber_email)

    const { contactId: createdContactId } = await this.createOrUpdateContact(request, foundedContactId, createOrUpdateContactBody)

    if (listId) {
      await this.addContactToSegment(request, listId, createdContactId)
    }
  }

  public async bindRemoveContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { subscriber_email } = payload.payload.data

    const { contactId } = await this.getContactIdByEmail(request, subscriber_email)

    await this.removeContact(request, contactId)
  }

  private async getContactIdByEmail(request: Request, contactEmail: string): Promise<Response> {
    const { data } = await axios.get(
            `${request.url}/contacts`,
            {
              headers: request.headers,
              params: {
                search: contactEmail
              }
            }
    )

    const foundContact = Object.values(data.contacts)[0]

    return { contactId: foundContact ? foundContact['id'] : 0 }
  }

  private async createOrUpdateContact (
    request: Request,
    contactId: string,
    createOrUpdateContactBody: CreateOrUpdateContactBody
  ): Promise<Response> {
    const { data: { contact } } = await axios.put(
            `${request.url}/contacts/${contactId}/edit`,
            createOrUpdateContactBody,
            {
              headers: request.headers,
            }
    )

    return { contactId: contact ? contact['id'] : 0 }
  }

  private async removeContact (request: Request, contactId: string): Promise<void> {
    await axios.delete(
            `${request.url}/contacts/${contactId}/delete`,
            {
              headers: request.headers,
            }
    )
  }

  private async addContactToSegment (
    request: Request,
    segmentId: string,
    contactId: string
  ): Promise<void> {
    await axios.post(
      `${request.url}/segments/${segmentId}/contact/${contactId}/add`,
      {},
      {
        headers: request.headers
      }
    )
  }
}
