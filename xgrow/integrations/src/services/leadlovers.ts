import axios from 'axios'
import { IInsertContact, IRemoveContact } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'

interface Request {
  url: string
  headers: {
    Accept: string
  }
}
interface Response {
  contactId: number
}
interface CreateOrUpdateContactBody {
  MachineCode: number
  EmailSequenceCode: number
  SequenceLevelCode: number
  Email: string
  Name: string
  Phone?: string
  City?: string
  State?: string
  Tags?: string[]
}

export class Leadlovers extends BaseService implements IInsertContact, IRemoveContact {
  private static readonly HTTP_ACCEPT = 'application/json'
  private static readonly BASE_URL = 'http://llapi.leadlovers.com/webapi'
  private static API_KEY = ''

  validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindInsertContact', 'bindRemoveContact'].includes(value)),
    'header.app.integration.api_key': (value: string) => !!value,
    'header.app.metadata.machineCode': (value: number) => !!value,
    'payload.data.subscriber_email': (value: string) => !!value
  }

  private request (payload: Payload): Request {
    const { api_key } = payload.header.app.integration
    Leadlovers.API_KEY = api_key

    const request: Request = {
      url: Leadlovers.BASE_URL,
      headers: {
        Accept: Leadlovers.HTTP_ACCEPT
      }
    }

    return request
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { tags, machineCode, sequenceCode, levelCode } = payload.header.app.metadata
    const {
      subscriber_email: contactEmail,
      subscriber_name: contactName,
      subscriber_phone: contactPhone,
      subscriber_city: contactCity,
      subscriber_state: contactState
    } = payload.payload.data

    const createOrUpdateContactBody: CreateOrUpdateContactBody = {
      MachineCode: machineCode,
      EmailSequenceCode: sequenceCode,
      SequenceLevelCode: levelCode,
      Email: contactEmail,
      Name: contactName,
      Phone: contactPhone,
      City: contactCity,
      State: contactState,
      Tags: tags
    }

    await this.createOrUpdateContact(request, createOrUpdateContactBody)
  }

  public async bindRemoveContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { subscriber_email: contactEmail } = payload.payload.data
    const { machineCode, sequenceCode } = payload.header.app.metadata

    await this.removeContact(request, contactEmail, machineCode, sequenceCode)
  }

  private async createOrUpdateContact (
    request: Request,
    createOrUpdateContactBody: CreateOrUpdateContactBody
  ): Promise<Response> {
    const { data: lead } = await axios.put(
      `${request.url}/Lead`,
      { ...createOrUpdateContactBody },
      {
        headers: request.headers,
        params: {
          token: Leadlovers.API_KEY
        }
      }
    )

    return { contactId: lead.Code }
  }

  private async removeContact (
    request: Request,
    contactEmail: string,
    machineCode: number,
    sequenceCode: number
  ): Promise<void> {
    await axios.delete(
      `${request.url}/Lead/Funnel`,
      {
        headers: request.headers,
        params: {
          token: Leadlovers.API_KEY,
          machineCode,
          sequenceCode,
          email: contactEmail
        }
      }
    )
  }
}
