import { CreatePersonUuidDto, UpdatePersonDto } from '@app/person/dtos'
export interface IPersonService {
  createOrUpdate(personDto: CreatePersonUuidDto)
  findOne(id: string)
  update(id: string, updatePersonDto: UpdatePersonDto)
}
