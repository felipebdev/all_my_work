import { ContactUuid } from '@app/contact/dtos'
import { Contact, ContactFilter } from '../contact.interface'
export interface IContactService {
  createPersonContacts(contactDto: ContactUuid)
  create(contactDto: Contact)
  findOneBy(filter: ContactFilter)
}
