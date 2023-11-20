import { Field, ArgsType } from '@nestjs/graphql'
import { IsString, IsUUID } from 'class-validator'

@ArgsType()
export class FileArgs {
  @IsUUID()
  @Field({ nullable: false })
  idPerson: string

  @IsUUID()
  @Field({ nullable: false })
  idProposal: string

  @IsString()
  @Field({ nullable: false })
  beneficiaryType: string
  
  @IsString()
  @Field({ nullable: false })
  saleType: string
}