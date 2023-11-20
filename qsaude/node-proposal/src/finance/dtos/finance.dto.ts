import { IsOnlyDate } from '@app/shared/validators/date'
import { PartialType } from '@nestjs/swagger'
import { IsEnum, IsNotEmpty, IsOptional, IsString, IsUUID } from 'class-validator'
import { FinanceFormPayment } from '@app/finance/interfaces/enum/finance.enum'
import { Transform } from 'class-transformer'
import moment from 'moment'

export class CreateFinanceDTO {
  @IsString()
  @IsEnum(FinanceFormPayment)
  @IsOptional()
  formPayment?: FinanceFormPayment

  @IsString()
  @IsOptional()
  dueDate?: string

  @IsOnlyDate()
  @Transform(({ value }) => moment(value).format('YYYY-MM-DD'), {
    toClassOnly: true
  })
  @IsOptional()
  startingDate?: string

  @IsUUID()
  @IsNotEmpty()
  idProposal: string
}

export class CreateFinanceUuidDto extends CreateFinanceDTO {
  @IsOptional()
  @IsUUID()
  idFinance: string
}
export class UpdateFinanceDTO extends PartialType(CreateFinanceDTO) {}

export class GetFinanceByProposalParamsDto {
  @IsUUID()
  idProposal: string
}
export class GetFinanceParamsDto {
  @IsUUID()
  idFinance?: string
}
