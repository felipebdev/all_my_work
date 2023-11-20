import axios from 'axios'
import { IInsertContact } from '../contracts/actions'
import { Payload } from '../job'
import { onlyNumbers } from '../utils/helper'
import { BaseService } from './base'

enum PersonType {
  NONE = 0,
  EMPLOYEE = 1,
  CUSTOMER = 2,
  HANDLER = 3,
  SYSTEM = 4,
  FORWARDING_EMPLOYEE = 5,
  FORWARDING_CUSTOMER = 6
}

enum PhoneType {
  CELL = 1,
  HOME = 2,
  BUSINESS = 3
}

enum PermissionView {
  MY_REQUEST = 1,
  MY_ORGANIZATION = 2
}

enum ParticipantPermission {
  NONE = 0,
  VIEW = 1,
  EDIT = 2
}

enum RoleType {
  NONE = 0,
  OWNER = 1,
  ADMIN = 2,
  MASTER_AGENT = 3,
  AGENT = 4,
  CLIENT = 5,
  CORPORATE_PERSON = 6
}

enum PermissionType {
  NONE = 0,
  ALL = 1,
  GROUP = 2,
  GROUP_PARTICIPATING = 3
}

interface Request {
  url: string
  headers: {
    Accept: string
    Authorization: string
  }
}

interface Response {
  personId?: string
}

interface Phone {
  number: string
  countryCode?: string
  type: PhoneType
}

interface CreatePerson {
  email: string
  name: string
  customerCode?: string
  type: PersonType
  phoneContacts: Phone
  permissionView: PermissionView
  participantPermission: ParticipantPermission
  roleType: RoleType
  permissionType: PermissionType
  organization: {
    name: string
  }
}

/**
 * @see https://api.octadesk.services/docs/
 */
export class Octadesk extends BaseService implements IInsertContact {
  private static readonly BASE_URL = 'https://api.octadesk.services'
  private static readonly HTTP_ACCEPT = 'application/json'
  private static readonly ORGANIZATION_NAME = 'Xgrow'
  protected validateSchema = {
    'header.app.action': value => (!!value && ['bindInsertContact'].includes(value)),
    'header.app.integration.api_key': value => !!value,
    'header.app.integration.api_account': value => !!value,
    'header.app.integration.api_secret': value => !!value,
    'payload.data.subscriber_id': value => !!value,
    'payload.data.subscriber_name': value => !!value,
    'payload.data.subscriber_email': value => !!value,
    'payload.data.subscriber_phone': value => !!value
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)
    const { personId } = await this.getPersonById(request, payload)
    if (!personId) await this.createPerson(request, payload)
  }

  private request (payload: Payload): Request {
    const {
      integration: {
        api_secret: apiSecret
      }
    } = payload.header.app

    const request: Request = {
      url: Octadesk.BASE_URL,
      headers: {
        Accept: Octadesk.HTTP_ACCEPT,
        Authorization: `Bearer ${apiSecret}`
      }
    }

    return request
  }

  /**
   * @see https://api.octadesk.services/docs/#/person/getPersonByEmail
   */
  private async getPersonById (
    request: Request,
    payload: Payload
  ): Promise<Response> {
    const {
      subscriber_email: subscriberEmail
    } = payload.payload.data

    try {
      const {
        data: {
          id = null
        } = {}
      } = await axios.get(
        `${request.url}/persons?email=${subscriberEmail}`,
        { headers: request.headers }
      )
      return { personId: id }
    } catch (error) {
      return { personId: null }
    }
  }

  /**
   * @see https://api.octadesk.services/docs/#/person/createPerson
   */
  private async createPerson (
    request: Request,
    payload: Payload
  ): Promise<void> {
    const {
      subscriber_id: subscriberId,
      subscriber_name: subscriberName,
      subscriber_email: subscriberEmail,
      subscriber_phone: subscriberPhone
    } = payload.payload.data

    const body: CreatePerson = {
      email: subscriberEmail,
      name: subscriberName,
      customerCode: subscriberId,
      type: PersonType.CUSTOMER,
      phoneContacts: {
        number: onlyNumbers(subscriberPhone),
        type: PhoneType.CELL
      },
      permissionView: PermissionView.MY_REQUEST,
      participantPermission: ParticipantPermission.NONE,
      roleType: RoleType.CLIENT,
      permissionType: PermissionType.NONE,
      organization: {
        name: Octadesk.ORGANIZATION_NAME
      }
    }

    await axios.post(
      `${request.url}/persons`,
      body,
      { headers: request.headers }
    )
  }
}
