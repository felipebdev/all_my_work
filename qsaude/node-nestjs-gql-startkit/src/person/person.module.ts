import { Module } from '@nestjs/common'
import { PersonService } from '@app/person/services/person.service'
import { PersonResolver } from '@app/person/resolvers/person.resolver'

@Module({
  providers: [PersonService, PersonResolver],
  exports: [PersonService]
})
export class PersonModule {}
