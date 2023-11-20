import { Field, ObjectType } from '@nestjs/graphql'

@ObjectType({ description: 'finance' })
export class Finance {
  @Field()
  idFinance: string

  @Field({ nullable: true })
  formPayment?: string

  @Field({ nullable: true })
  dueDate?: string

  @Field({ nullable: true })
  startingDate?: string

  @Field()
  idProposal: string
}
