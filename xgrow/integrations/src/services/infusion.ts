import axios from 'axios'
import { IInsertContactTag, IRemoveContactTag } from '../contracts/actions'
import { Payload } from '../job'
import { onlyNumbers } from '../utils/helper'
import { BaseService } from './base'

enum SourceType {
  APPOINTMENT = 'APPOINTMENT',
  FORMAPIHOSTED = 'FORMAPIHOSTED',
  FORMAPIINTERNAL = 'FORMAPIINTERNAL',
  WEBFORM = 'WEBFORM',
  INTERNALFORM = 'INTERNALFORM',
  LANDINGPAGE = 'LANDINGPAGE',
  IMPORT = 'IMPORT',
  MANUAL = 'MANUAL',
  API = 'API',
  OTHER = 'OTHER',
  UNKNOWN = 'UNKNOWN'
}

enum DuplicateOption {
  EMAIL = 'Email',
  EMAIL_NAME = 'EmailAndName'
}

interface EmailAddress {
  email: string
  field: string
}

interface PhoneNumber {
  extension?: string
  field: string
  number: string
  type?: string
}

interface Request {
  url: string
  headers: {
    Accept: string
    Authorization: string
  }
}

interface Response {
  contactId?: string
  tagIds?: string[]
}

interface CreateOrUpdateContactBody {
  given_name: string
  opt_in_reason: string
  source_type: SourceType
  duplicate_option: DuplicateOption
  email_addresses: EmailAddress[]
  phone_numbers: PhoneNumber[]
}

interface AddTagToContactBody {
  contactId: string
  tagsId: string[]
}

export class Infusion extends BaseService implements IInsertContactTag, IRemoveContactTag {
  private static readonly BASE_URL = 'https://api.infusionsoft.com/crm/rest/v1'
  private static readonly HTTP_ACCEPT = 'application/json'
  private static readonly OPT_IN_REASON = 'Customer opted-in through Xgrow app'
  protected validateSchema = {
    'header.app.action': value => (!!value && ['bindInsertContactTag', 'bindRemoveContactTag'].includes(value)),
    'header.app.integration.api_key': value => !!value,
    'header.app.metadata.tags': value => !!value,
    'payload.data.subscriber_name': value => !!value,
    'payload.data.subscriber_email': value => !!value,
    'payload.data.subscriber_phone': value => !!value
  }

  public async bindInsertContactTag (payload: Payload): Promise<void> {
    const request = this.request(payload)
    const { contactId } = await this.createOrUpdateContact(request, payload)
    if (contactId) {
      const {
        metadata: { tags }
      } = payload.header.app

      const body: AddTagToContactBody = {
        contactId: contactId,
        tagsId: tags
      }

      await this.applyTagsToContact(request, body)
    }
  }

  public async bindRemoveContactTag (payload: Payload): Promise<void> {
    const request = this.request(payload)
    const { contactId, tagIds } = await this.createOrUpdateContact(request, payload)
    if (contactId) {
      let {
        metadata: { tags }
      } = payload.header.app
      tags = tags.map((tag: string) => Number(tag))
      const tagsToRemove = tagIds.filter(tag => tags.includes(tag)) // intersect contact tags and action tags
      if (tagsToRemove.length > 0) {
        const body: AddTagToContactBody = {
          contactId: contactId,
          tagsId: tagsToRemove
        }

        await this.removeTagsFromContact(request, body)
      }
    }
  }

  private request (payload: Payload): Request {
    const {
      integration: {
        api_key: apiKey
      }
    } = payload.header.app

    const request: Request = {
      url: Infusion.BASE_URL,
      headers: {
        Accept: Infusion.HTTP_ACCEPT,
        Authorization: `Bearer ${apiKey}`
      }
    }

    return request
  }

  /**
   * @see https://developer.infusionsoft.com/docs/rest/#!/Contact/createOrUpdateContactUsingPUT
   */
  private async createOrUpdateContact (
    request: Request,
    payload: Payload
  ): Promise<Response> {
    const {
      subscriber_name: subscriberName,
      subscriber_email: subscriberEmail,
      subscriber_phone: subscriberPhone
    } = payload.payload.data

    const contact: CreateOrUpdateContactBody = {
      given_name: subscriberName,
      source_type: SourceType.API,
      opt_in_reason: Infusion.OPT_IN_REASON,
      duplicate_option: DuplicateOption.EMAIL,
      email_addresses: [{
        email: subscriberEmail,
        field: 'EMAIL1'
      }],
      phone_numbers: [{
        number: onlyNumbers(subscriberPhone),
        field: 'PHONE1'
      }]
    }

    const {
      data: {
        id: contactId = null,
        tag_ids: tagIds = []
      } = {}
    } = await axios.put(
      `${request.url}/contacts`,
      contact,
      { headers: request.headers }
    )

    return { contactId, tagIds }
  }

  /**
   * @see https://developer.infusionsoft.com/docs/rest/#!/Contact/applyTagsToContactIdUsingPOST
   */
  private async applyTagsToContact (
    request: Request,
    payload: AddTagToContactBody
  ): Promise<void> {
    await axios.post(
      `${request.url}/contacts/${payload.contactId}/tags`,
      { tagIds: payload.tagsId },
      { headers: request.headers }
    )
  }

  /**
   * @see https://developer.infusionsoft.com/docs/rest/#!/Contact/removeTagsFromContactUsingDELETE
   */
  private async removeTagsFromContact (
    request: Request,
    payload: AddTagToContactBody
  ): Promise<void> {
    const tagsId = payload.tagsId.join()
    await axios.delete(
      `${request.url}/contacts/${payload.contactId}/tags?ids=${tagsId}`,
      { headers: request.headers }
    )
  }
}
