import axios from 'axios'
import { IInsertContact, IRemoveContact } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'

enum Status {
  SUBSCRIBE = 1,
  UNSUBSCRIBE = 2
}

interface Request {
  url: string
  headers: {
    Accept: string
    'Api-Token': string
  }
}

interface Response {
  contactId: number
  listId?: number
  contactTags?: any[]
}

interface CustomFieldValue {
  field: string
  value: string
}

interface CreateOrUpdateContactBody {
  email: string
  firstName: string
  lastName?: string
  phone?: string
  fieldValues?: CustomFieldValue[]
}

interface UpdateListStatusForContactBody {
  list: number
  contact: number
  status: Status
}

interface AddTagToContactBody {
  contact: number
  tag: number
}

/**
 * @see https://developers.activecampaign.com/reference#overview
 */
export class Activecampaign extends BaseService implements IInsertContact, IRemoveContact {
  private static readonly HTTP_ACCEPT = 'application/json'
  protected validateSchema = {
    'header.app.action': value => (!!value && ['bindInsertContact', 'bindRemoveContact'].includes(value)),
    // 'header.app.metadata.list': value => !!value,
    'header.app.integration.api_key': value => !!value,
    'header.app.integration.api_webhook': value => !!value,
    'payload.data.subscriber_name': value => !!value,
    'payload.data.subscriber_email': value => !!value,
    'payload.data.subscriber_phone': value => !!value
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)
    const { contactId } = await this.postback(request, payload, Status.SUBSCRIBE)

    const {
      metadata: {
        tags = []
      } = {}
    } = payload.header.app

    for (const tag of tags) {
      const contactTag: AddTagToContactBody = {
        contact: contactId,
        tag: Number(tag)
      }
      await this.addTagToContact(request, contactTag)
    }
  }

  public async bindRemoveContact (payload: Payload): Promise<void> {
    const request = this.request(payload)
    const { contactId } = await this.postback(request, payload, Status.UNSUBSCRIBE)

    const {
      metadata: {
        tags = []
      } = {}
    } = payload.header.app

    const contactTags = await this.getContactTags(request, contactId)
    for (const tag of tags) {
      if (tag in contactTags) {
        const contactTag: AddTagToContactBody = {
          contact: contactId,
          tag: Number(contactTags[tag])
        }
        await this.removeTagFromContact(request, contactTag)
      }
    }
  }

  private request (payload: Payload): Request {
    const {
      integration: {
        api_key: apiKey,
        api_webhook: apiWebhook
      }
    } = payload.header.app

    const request: Request = {
      url: apiWebhook,
      headers: {
        Accept: Activecampaign.HTTP_ACCEPT,
        'Api-Token': apiKey
      }
    }

    return request
  }

  private async postback (
    request: Request,
    payload: Payload,
    status: Status
  ): Promise<Response> {
    const {
      metadata: {
        list: listId = null
      } = {}
    } = payload.header.app

    const {
      subscriber_name: subscriberName,
      subscriber_email: subscriberEmail,
      subscriber_phone: subscriberPhone,
      transaction_origin: transactionOrigin,
      change_card_url: changeCardUrl
    } = payload.payload.data

    const { metadata: { change_card_field = null } = {} } = payload.header.app

    const contact: CreateOrUpdateContactBody = {
      email: subscriberEmail,
      firstName: subscriberName,
      phone: subscriberPhone,
      ...((status === Status.SUBSCRIBE && ![null, undefined, '', 'transaction'].includes(transactionOrigin) && ![null, undefined, ''].includes(change_card_field)) && {
        fieldValues: [{
          field: change_card_field,
          value: changeCardUrl
        }]
      })
    }

    const { contactId } = await this.createOrUpdateContact(request, contact)
    if (!contactId) throw new Error('An error ocurred when try to create/update a contact')

    if (listId) {
      const contactList: UpdateListStatusForContactBody = {
        list: Number(listId),
        contact: contactId,
        status
      }

      await this.updateListStatusForContact(request, contactList)
    }

    return { contactId, listId }
  }

  /**
   * @see https://developers.activecampaign.com/reference/sync-a-contacts-data
   */
  private async createOrUpdateContact (
    request: Request,
    payload: CreateOrUpdateContactBody
  ): Promise<Response> {
    const {
      data: {
        contact: {
          id: contactId = null
        } = {}
      } = {}
    } = await axios.post(
      `${request.url}/api/3/contact/sync`,
      { contact: payload },
      { headers: request.headers }
    )

    return { contactId: Number(contactId) }
  }

  /**
   * @see https://developers.activecampaign.com/reference#update-list-status-for-contact
   */
  private async updateListStatusForContact (
    request: Request,
    payload: UpdateListStatusForContactBody
  ): Promise<number | null> {
    const {
      data: {
        contactList: {
          list: listId = null
        } = {}
      } = {}
    } = await axios.post(
      `${request.url}/api/3/contactLists`,
      { contactList: payload },
      { headers: request.headers }
    )

    return Number(listId)
  }

  /**
   * @see https://developers.activecampaign.com/reference#create-contact-tag
   */
  private async addTagToContact (
    request: Request,
    payload: AddTagToContactBody
  ): Promise<void> {
    await axios.post(
      `${request.url}/api/3/contactTags`,
      { contactTag: payload },
      { headers: request.headers }
    )
  }

  private async getContactTags (
    request: Request,
    contactId: number
  ): Promise<object> {
    const {
      data: {
        contactTags = []
      } = {}
    } = await axios.get(
      `${request.url}/api/3/contacts/${contactId}/contactTags`,
      { headers: request.headers }
    )

    const tags = {}
    contactTags.forEach(({ id, tag }) => { tags[tag] = id })
    return tags
  }

  /**
   * @see https://developers.activecampaign.com/reference#delete-contact-tag
   */
  private async removeTagFromContact (
    request: Request,
    payload: AddTagToContactBody
  ): Promise<void> {
    await axios.delete(
      `${request.url}/api/3/contactTags/${payload.tag}`,
      { headers: request.headers }
    )
  }
}
