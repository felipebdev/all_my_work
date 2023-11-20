import { Controller, Post, Body } from '@nestjs/common'
import { CONTACT_CONTROLLER } from '@app/contact/constants'
import { ContactUuid } from '@app/contact/dtos'
import { AbstractContactService } from '../services/abstract/contact.service.abstract'

@Controller(CONTACT_CONTROLLER)
export class ContactController {
  constructor(private readonly contactService: AbstractContactService) {}
  @Post('/person/proposal-role')
  async createPersonContacts(@Body() contactDto: ContactUuid) {
    return this.contactService.createPersonContacts(contactDto)
  }
}
