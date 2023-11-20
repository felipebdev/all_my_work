import { Controller, Post, Body, Get, Param, Patch } from '@nestjs/common'
import { PERSON_CONTROLLER } from '@app/person/constants'
import { CreatePersonUuidDto, UpdatePersonDto } from '@app/person/dtos'
import { AbstractPersonService } from '@app/person/services'

@Controller(PERSON_CONTROLLER)
export class PersonController {
  constructor(private readonly personService: AbstractPersonService) {}

  @Get(':id')
  async findOne(@Param('id') id: string) {
    return this.personService.findOne(id)
  }

  @Post()
  async createPerson(@Body() personDto: CreatePersonUuidDto) {
    return this.personService.createOrUpdate(personDto)
  }

  @Patch(':id')
  async updatePerson(@Param('id') id: string, @Body() updatePersonDto: UpdatePersonDto) {
    return this.personService.update(id, updatePersonDto)
  }
}
