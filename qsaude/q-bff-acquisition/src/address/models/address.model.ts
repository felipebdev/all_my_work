import { ObjectType, Field } from '@nestjs/graphql'

@ObjectType()
export class Address {
  @Field({ nullable: true })
  address: string

  @Field({ nullable: true })
  addressNumber?: string

  @Field({ nullable: true })
  addressComplement?: string

  @Field({ nullable: true })
  city: string

  @Field({ nullable: true })
  neighborhood: string

  @Field({ nullable: true })
  state: string

  @Field({ nullable: true })
  zipCode: string
}
