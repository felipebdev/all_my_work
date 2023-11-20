import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { PersonEntity } from '@app/person/entities'
import { Repository } from 'typeorm'
import { CreatePersonUuidDto, UpdatePersonDto } from '@app/person/dtos'
import { AbstractPersonService } from '@app/person/services'

@Injectable()
export class PersonService extends AbstractPersonService {
  constructor(
    @InjectRepository(PersonEntity)
    private readonly personRepository: Repository<PersonEntity>
  ) {
    super()
  }

  async findOne(idPerson: string): Promise<PersonEntity> {
    const person = await this.personRepository.findOne({ where: { idPerson } })
    if (!person) {
      throw new NotFoundException(`Person #${idPerson} was not found`)
    }
    return person
  }

  async createOrUpdate(personDto: CreatePersonUuidDto): Promise<PersonEntity> {
    try {
      const person = this.personRepository.create(personDto)
      return await this.personRepository.save(person)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }

  async update(idPerson: string, updatePersonDto: UpdatePersonDto): Promise<PersonEntity> {
    try {
      const person = await this.personRepository.preload({
        idPerson,
        ...updatePersonDto
      })
      return await this.personRepository.save(person)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }
}
