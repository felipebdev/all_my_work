import { CompanySize } from '@app/company/interfaces'
import { Field, ObjectType } from '@nestjs/graphql'

@ObjectType({ description: 'company' })
export class Company {
  @Field()
  idCompany: string

  @Field({ nullable: true })
  cnpj?: string

  @Field({ nullable: true })
  name?: string

  @Field({ nullable: true })
  tradeName?: string

  @Field({ nullable: true })
  codeLegalNature?: string

  @Field({ nullable: true })
  companySize?: CompanySize

  @Field({ nullable: true })
  cnae?: string

  @Field({ nullable: true })
  openingDate?: string

  @Field()
  idProposal: string
}
