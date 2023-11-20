import axios from 'axios'
import { IInsertContact, IInsertContactTag, IRemoveContact, IRemoveContactTag } from '../contracts/actions'
import { Payload } from '../job'
import { BaseService } from './base'
import crypto from 'crypto'

enum Status {
  SUBSCRIBED = 'subscribed',
  UNSUBSCRIBED = 'unsubscribed',
  CLEANED = 'cleaned',
  PENDING = 'pending',
  TRANSACTIONAL = 'transactional'
}

interface Request {
  url: string
  headers: {
    Accept: string
    Authorization: string
  }
}

interface Response {
  contactId: string
  listId?: string
  contactTags?: Array<{ id: number, name: string }>
}

interface CreateContactBody {
  email_address?: string
  status_if_new?: Status
  status?: Status
  merge_fields?: {
    FNAME: string
    LNAME?: string
  }
}

interface AddOrRemoveTagsBody {
  listId: string
  contactId: string
  tags: Array<{ name: string, status: string }>
}

/**
 * @see https://mailchimp.com/developer/marketing/api
 */
export class Mailchimp extends BaseService implements IInsertContact, IInsertContactTag, IRemoveContact, IRemoveContactTag {
  private static readonly HTTP_ACCEPT = 'application/json'

  protected validateSchema = {
    'header.app.action': (value: string) => (!!value && ['bindInsertContact', 'bindInsertContactTag', 'bindRemoveContact', 'bindRemoveContactTag'].includes(value)),
    'header.app.metadata.list': (value: string) => !!value,
    'header.app.integration.api_key': (value: string) => !!value,
    'payload.data.subscriber_email': (value: string) => !!value
  }

  private request (payload: Payload): Request {
    const { api_key } = payload.header.app.integration
    const splitedApiKey = api_key.split('-')
    const mailChimpDataCenterCode = splitedApiKey[splitedApiKey.length - 1]

    const request: Request = {
      url: `https://${mailChimpDataCenterCode}.api.mailchimp.com/3.0`,
      headers: {
        Accept: Mailchimp.HTTP_ACCEPT,
        Authorization: `Bearer ${api_key}`
      }
    }

    return request
  }

  public async bindInsertContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { subscriber_email: email_address } = payload.payload.data

    const createContactBody: CreateContactBody = {
      email_address,
      status: Status.SUBSCRIBED,
      status_if_new: Status.SUBSCRIBED
    }

    const { subscriber_name: fullName } = payload.payload.data

    if (fullName) {
      const splitedName = fullName.split(' ')
      Object.assign(createContactBody, {
        merge_fields: {
          FNAME: splitedName[0],
          LNAME: splitedName.length > 1 ? splitedName[splitedName.length - 1] : ''
        }
      })
    }

    const contactId = this.getContactIdFromEmail(email_address)
    const { list: listId } = payload.header.app.metadata

    await this.createOrUpdateContact(request, listId, contactId, createContactBody)

    await this.bindInsertContactTag(payload)
  }

  public async bindRemoveContact (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { list: listId } = payload.header.app.metadata
    const { subscriber_email: email_address } = payload.payload.data

    const contactId = this.getContactIdFromEmail(email_address)

    await this.createOrUpdateContact(request, listId, contactId, { status: Status.UNSUBSCRIBED })

    await this.bindRemoveContactTag(payload)
  }

  public async bindInsertContactTag (payload: Payload): Promise<void> {
    const request = this.request(payload)

    const { list: listId, tags } = payload.header.app.metadata
    const { subscriber_email: email_address } = payload.payload.data

    const contactId = this.getContactIdFromEmail(email_address)

    const contactTags: AddOrRemoveTagsBody = {
      listId,
      contactId,
      tags: tags ? tags.map((tag: string) => ({ name: tag, status: 'active' })) : []
    }
    await this.addOrRemoveTags(request, contactTags)
  }

  public async bindRemoveContactTag (payload: Payload): Promise<void> {
    const request = this.request(payload)
    const { list: listId, tags } = payload.header.app.metadata
    const { subscriber_email: email_address } = payload.payload.data

    const contactId = this.getContactIdFromEmail(email_address)

    const contactTags: AddOrRemoveTagsBody = {
      listId,
      contactId,
      tags: tags ? tags.map((tag: string) => ({ name: tag, status: 'inactive' })) : []
    }

    await this.addOrRemoveTags(request, contactTags)
  }

  /**
   * @see https://mailchimp.com/developer/marketing/api/add-or-update-list-member/
  */
  private async createOrUpdateContact (
    request: Request,
    listId: string,
    contactId: string,
    createContactBody: CreateContactBody
  ): Promise<Response> {
    const { data: contact } = await axios.put(
      `${request.url}/lists/${listId}/members/${contactId}`,
      { ...createContactBody },
      { headers: request.headers }
    )

    return {
      contactId: contact.id,
      listId: contact.list_id,
      contactTags: contact.tags ? contact.tags.map((tag: { id: string, name: string }) => tag.name) : []
    }
  }

  /**
   * @see https://mailchimp.com/developer/marketing/api/list-member-tags/add-or-remove-member-tags/
  */
  private async addOrRemoveTags (
    request: Request,
    payload: AddOrRemoveTagsBody
  ): Promise<void> {
    const { listId, contactId, tags } = payload

    await axios.post(
      `${request.url}/lists/${listId}/members/${contactId}/tags`,
      { tags },
      { headers: request.headers }
    )
  }

  private getContactIdFromEmail (emailAddress: string): string {
    return crypto.createHash('md5').update(emailAddress.toLowerCase()).digest('hex')
  }
}
