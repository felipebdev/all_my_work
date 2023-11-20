import { ProposalRoleEnum } from '@app/proposal/interfaces/enums/proposal.enum'
import { InputType, Field, ArgsType, registerEnumType } from '@nestjs/graphql'
import { IsEnum, IsString, IsUUID } from 'class-validator'

registerEnumType(ProposalRoleEnum, {
  name: 'ProposalRoleEnum'
})

@InputType({ isAbstract: true })
export class AddressInput {
  @IsString()
  @Field()
  address: string

  @IsString()
  @Field()
  addressNumber: string

  @IsString()
  @Field()
  addressComplement: string

  @IsString()
  @Field()
  city: string

  @IsString()
  @Field()
  neighborhood: string

  @IsString()
  @Field()
  state: string

  @IsString()
  @Field()
  zipCode: string
}

@InputType({ description: 'person address input' })
export class PersonAddressInput extends AddressInput {
  @IsUUID()
  @Field()
  idProposal: string

  @IsEnum(ProposalRoleEnum)
  @Field(() => ProposalRoleEnum)
  role: ProposalRoleEnum
}

@InputType({ description: 'company address input' })
export class CompanyAddressInput extends AddressInput {
  @IsUUID()
  @Field()
  idCompany: string
}

@ArgsType()
export class AddressArgs {
  @IsUUID()
  @Field({ nullable: false })
  idProposal: string

  @IsEnum(ProposalRoleEnum)
  @Field(() => ProposalRoleEnum)
  role: ProposalRoleEnum
}
