import { ContactUuid } from '@app/contact/dtos'
import { ContactEntity } from '@app/contact/entities'
import { IContactService } from '@app/contact/interfaces'
import { Contact, ContactFilter } from '@app/contact/interfaces/contact.interface'
export abstract class AbstractContactService implements IContactService {
  abstract createPersonContacts(contactDto: ContactUuid)
  abstract create(contactDto: Contact)
  abstract findOneBy(filter: ContactFilter): Promise<ContactEntity>
}
