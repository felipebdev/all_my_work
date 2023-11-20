import { ContactUuid } from '@app/contact/dtos'
import { ContactEntity } from '@app/contact/entities'
import { ContactRefType, ContactType } from '@app/contact/interfaces/contact.enum'
import { Contact, ContactFilter } from '@app/contact/interfaces/contact.interface'
import { AbstractContactService } from '@app/contact/services/abstract/contact.service.abstract'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'

@Injectable()
export class ContactService implements AbstractContactService {
  constructor(
    @InjectRepository(ContactEntity)
    private readonly contactRepository: Repository<ContactEntity>
  ) {}

  async create(contactDto: Contact): Promise<ContactEntity> {
    const contact = this.contactRepository.create(contactDto)
    return this.contactRepository.save(contact)
  }

  async createPersonContacts(contactDto: ContactUuid): Promise<ContactEntity[]> {
    try {
      const { email, cellphone, refId } = contactDto
      const emailContact = this.contactRepository.create({
        type: ContactType.email,
        value: email,
        refType: ContactRefType.proposalRole,
        refId: refId
      })
      const cellphoneContact = this.contactRepository.create({
        type: ContactType.cellphone,
        value: cellphone,
        refType: ContactRefType.proposalRole,
        refId: refId
      })
      return await this.contactRepository.save([emailContact, cellphoneContact])
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }

  async findOneBy(filter: ContactFilter): Promise<ContactEntity> {
    const contact = await this.contactRepository.findOneBy(filter)
    if (!contact) throw new NotFoundException(`Contact was not found`)
    return contact
  }
}
