import axios from 'axios'
import { IInsertContact, IRemoveContact } from '../contracts/actions'
import { Payload } from '../job'
import { onlyNumbers } from '../utils/helper'
import { BaseService } from './base'

interface Request {
  url: string
  headers: { Accept: string }
  params: { api_key: string }
}

enum Status {
  INACTIVE = 'inactive',
  PENDING = 'pending',
  ACTIVE = 'active',
  EXPIRED = 'expired'
}

interface ContactBody {
  full_name: string
  email: string
  status: Status
  blocked?: boolean
  unlimited?: boolean
  classroom_ids?: number[]
  membership_level_id?: number
  expires_at?: string

}

interface CreateContactBody extends ContactBody {
  cpf_cnpj?: string // onlyNumbers
  phone_local_code?: string // DDD
  phone_number?: string // onlyNumbers
}

interface UpdateContactBody extends ContactBody {
  metadata: {
    cpf_cnpj?: string // onlyNumbers
    phone_local_code?: string // DDD
    phone_number?: string // onlyNumbers
  }
}

export class Memberkit extends BaseService implements IInsertContact, IRemoveContact {
  protected validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindInsertContact', 'bindRemoveContact'].includes(value)),
    'header.app.integration.api_key': (value: string) => !!value,
    'payload.data.subscriber_name': (value: string) => !!value,
    'payload.data.subscriber_email': (value: string) => !!value
  }

  private request(payload: Payload): Request {
    const { api_key } = payload.header.app.integration
    return {
      url: 'https://memberkit.com.br/api/v1',
      headers: {
        Accept: 'application/json',
      },
      params: { api_key }
    }
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    let status: Status = Status.ACTIVE
    const integrationEvent = payload.header.app.event

    if (integrationEvent === 'onCreateLead') {
      status = Status.PENDING
    }

    const listId = payload.header.app.metadata?.list

    const {
      subscriber_name,
      subscriber_email,
      subscriber_document_number,
      subscriber_phone,
    } = payload.payload.data

    const contactPhone = onlyNumbers(subscriber_phone)

    const contactBody: ContactBody = {
      full_name: subscriber_name,
      email: subscriber_email,
      status,
      classroom_ids: [+listId]
    }

    const memberExists = await this.getMember(request, subscriber_email)

    if (memberExists) {
      const updateContactBody: UpdateContactBody = {
        ...contactBody,
        metadata: {
          cpf_cnpj: subscriber_document_number,
          phone_number: contactPhone.slice(2),
          phone_local_code: contactPhone.slice(0, 2)
        }
      }

      await this.updateMember(request, updateContactBody)
    } else {
      const createContactBody: CreateContactBody = {
        ...contactBody,
        cpf_cnpj: subscriber_document_number,
        phone_number: contactPhone.slice(2),
        phone_local_code: contactPhone.slice(0, 2)
      }

      await this.createMember(request, createContactBody)
    }
  }

  public async bindRemoveContact(payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { subscriber_email } = payload.payload.data

    await this.removeMember(request, subscriber_email)
  }

  private async getMember (request: Request, contactEmail: string): Promise<boolean> {
    return await axios.get(
      `${request.url}/users/${contactEmail}`,
      {
        headers: request.headers,
        params: request.params,
      }
    ).then(() => true).catch((_err) => false)
  }

  private async createMember (request: Request, createContactBody: CreateContactBody): Promise<void> {
    await axios.post(
      `${request.url}/users`,
      { ...createContactBody },
      {
        headers: request.headers,
        params: request.params,
      }
    )
  }

  private async updateMember (request: Request, updateContactBody: UpdateContactBody): Promise<void> {
    await axios.put(
      `${request.url}/users/${updateContactBody.email}`,
      { ...updateContactBody },
      {
        headers: request.headers,
        params: request.params,
      }
    )
  }

  private async removeMember (request: Request, contactEmail: string): Promise<void> {
    await axios.delete(
      `${request.url}/users/${contactEmail}`,
      {
        headers: request.headers,
        params: request.params,
      }
    )
  }
}
