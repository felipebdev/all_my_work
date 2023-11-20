import { CreatePersonUuidDto, UpdatePersonDto } from '@app/person/dtos'
import { IPersonService } from '@app/person/interfaces'
export abstract class AbstractPersonService implements IPersonService {
  abstract createOrUpdate(personDto: CreatePersonUuidDto)
  abstract findOne(id: string)
  abstract update(id: string, updatePersonDto: UpdatePersonDto)
}
