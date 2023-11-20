import { Person } from '@app/person/models/person.model'
import { ProposalRole } from '@app/proposal/models/proposal-role.model'
import { ObjectType, Field } from '@nestjs/graphql'

@ObjectType()
export class Proposal {
  @Field({ nullable: true })
  idProposal?: string

  @Field({ nullable: true })
  idLead?: string

  @Field({ nullable: true })
  proposalNumber?: string

  @Field({ nullable: true })
  levelSale?: number

  @Field({ nullable: true })
  createdAt?: Date

  @Field({ nullable: true })
  updatedAt?: Date
}

@ObjectType()
export class CreatedProposal {
  @Field()
  proposal: Proposal

  @Field()
  person: Person

  @Field()
  proposalRole: ProposalRole

  @Field()
  token: boolean
}
