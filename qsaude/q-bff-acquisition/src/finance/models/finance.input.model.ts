import { FinanceFormPayment } from '@app/finance/interfaces/enum/finance.enum'
import { InputType, Field, ArgsType, registerEnumType, PartialType } from '@nestjs/graphql'
import { IsEnum, IsNotEmpty, IsOptional, IsString, IsUUID } from 'class-validator'

registerEnumType(FinanceFormPayment, {
  name: 'FinanceFormPayment'
})

@InputType({ description: 'finance input' })
export class FinanceInput {
  @IsString()
  @IsEnum(FinanceFormPayment)
  @IsOptional()
  @Field({ nullable: true })
  formPayment?: FinanceFormPayment

  @IsString()
  @IsOptional()
  @Field({ nullable: true })
  dueDate?: string

  @IsString()
  @IsOptional()
  @Field({ nullable: true })
  startingDate?: string

  @IsUUID()
  @IsNotEmpty()
  @Field()
  idProposal: string
}

@InputType()
export class UpdateFinanceInput extends PartialType(FinanceInput) {}

@ArgsType()
export class FinanceArgs {
  @IsUUID()
  @Field({ nullable: false })
  id: string
}
