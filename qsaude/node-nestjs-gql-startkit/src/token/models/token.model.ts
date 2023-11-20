import { Field, ObjectType } from '@nestjs/graphql'

@ObjectType({ description: 'token' })
export class Token {
  @Field()
  value: string

  @Field()
  cpf: string

  @Field()
  expiredIn: string

  @Field()
  id: string

  @Field()
  type: string
}
