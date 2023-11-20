import { ProposalClass } from '@app/proposal/interfaces/enums/proposal.enum'
import { Field, InputType, registerEnumType } from '@nestjs/graphql'
import { IsOptional, IsString, IsEnum, IsDefined } from 'class-validator'
import { CheckTokenInput } from '@app/token/models/token.input.model'
import { PersonInput } from '@app/person/models/person.input.model'

@InputType({ description: 'proposal' })
export class ProposalInput {
  @IsString()
  @Field({ nullable: true })
  idLead?: string

  @IsString()
  @Field({ nullable: true })
  levelSale?: number
}

@InputType({ description: 'legal-representative' })
export class LegalRepresentativeInput {
  @IsDefined()
  @Field()
  proposal: ProposalInput

  @IsDefined()
  @Field()
  tokenValidation: CheckTokenInput

  @IsDefined()
  @Field()
  person: PersonInput
}
