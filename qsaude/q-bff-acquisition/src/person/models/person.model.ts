import { Field, ObjectType } from '@nestjs/graphql'

@ObjectType({ description: 'person' })
export class Person {
  @Field({ nullable: true })
  idPerson?: string

  @Field({ nullable: true })
  name?: string

  @Field({ nullable: true })
  birthday?: string

  @Field({ nullable: true })
  gender?: string

  @Field({ nullable: true })
  maritalStatus?: string

  @Field({ nullable: true })
  cpf?: string

  @Field({ nullable: true })
  cns?: string

  @Field({ nullable: true })
  rg?: string

  @Field({ nullable: true })
  emittingOrgan?: string

  @Field({ nullable: true })
  motherName?: string

  @Field({ nullable: true })
  email?: string

  @Field({ nullable: true })
  cellphone?: string
}
