import { Person } from '@app/person/models/person.model'
import { PersonService } from '@app/person/services/person.service'
import { Query, Resolver, Args } from '@nestjs/graphql'

@Resolver()
export class PersonResolver {
  constructor(private readonly personService: PersonService) {}

  @Query(() => Person, { nullable: false })
  async person(@Args('id', { type: () => String }) id: string): Promise<Person> {
    return this.personService.getPersonById(id) as unknown as Person
  }
}
