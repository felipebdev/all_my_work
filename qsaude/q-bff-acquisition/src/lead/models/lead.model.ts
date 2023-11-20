import { Field, ObjectType } from '@nestjs/graphql'

@ObjectType({ description: 'lead' })
export class Lead {
  @Field({ nullable: true })
  uuidLead: string

  @Field({ nullable: true })
  numberCPF: string

  @Field({ nullable: true })
  completeName: string

  @Field({ nullable: true })
  birthday: string

  @Field({ nullable: true })
  codePlan: string

  @Field({ nullable: true })
  email: string

  @Field({ nullable: true })
  CellPhoneDDD: string

  @Field({ nullable: true })
  cellPhoneNumber: string

  @Field({ nullable: true })
  tagName: string
}
