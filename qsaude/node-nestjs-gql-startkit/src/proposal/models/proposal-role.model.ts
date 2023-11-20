import { ObjectType, Field } from '@nestjs/graphql'

@ObjectType()
export class ProposalRole {
  @Field()
  idProposalRole: string

  @Field()
  role: string

  @Field()
  idProposal: string

  @Field()
  idPerson: string

  @Field()
  createdAt: Date

  @Field()
  updatedAt: Date
}
