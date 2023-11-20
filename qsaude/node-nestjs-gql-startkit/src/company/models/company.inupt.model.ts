import { CompanySize } from '@app/company/interfaces'
import { InputType, Field, ArgsType, registerEnumType, PartialType } from '@nestjs/graphql'
import { IsEnum, IsNotEmpty, IsOptional, IsString, IsUUID, MaxLength } from 'class-validator'

registerEnumType(CompanySize, {
  name: 'CompanySize'
})

@InputType({ description: 'company input' })
export class CompanyInput {
  @IsString()
  @IsNotEmpty()
  @IsOptional()
  @Field({ nullable: true })
  cnpj?: string

  @IsString()
  @IsNotEmpty()
  @IsOptional()
  @Field({ nullable: true })
  name?: string

  @IsString()
  @IsNotEmpty()
  @IsOptional()
  @Field({ nullable: true })
  tradeName?: string

  @MaxLength(5)
  @IsString()
  @IsNotEmpty()
  @IsOptional()
  @Field({ nullable: true })
  codeLegalNature?: string

  @IsString()
  @IsEnum(CompanySize)
  @IsNotEmpty()
  @IsOptional()
  @Field({ nullable: true })
  companySize?: CompanySize

  @IsString()
  @IsNotEmpty()
  @IsOptional()
  @Field({ nullable: true })
  cnae?: string

  @IsString()
  @IsNotEmpty()
  @IsOptional()
  @Field({ nullable: true })
  openingDate?: string

  @IsUUID()
  @IsNotEmpty()
  @Field()
  idProposal: string
}

@InputType()
export class UpdateCompanyInput extends PartialType(CompanyInput) {}

@ArgsType()
export class CompanyArgs {
  @IsUUID()
  @Field({ nullable: false })
  id: string
}
