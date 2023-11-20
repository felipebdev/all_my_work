import { IsStringNumber } from '@app/common/validators'
import { IsCNPJOrCPF } from '../../common/validators/brazilian-document'
import { IsNotEmpty, IsString, IsIn, ValidateIf, Length, MaxLength, IsOptional } from 'class-validator'

const documentTypeOptions = ['cpf', 'cnpj']
const accountTypeOptions = ['checkings', 'savings']

export class UserDocumentsDTO {
  @IsNotEmpty()
  @IsString()
  @ValidateIf((o) => o['document_type'] === 'cnpj')
  company_name: string

  @IsNotEmpty()
  @IsString()
  @MaxLength(30)
  @ValidateIf((o) => o['document_type'] === 'cnpj')
  legal_name: string

  @IsNotEmpty()
  @IsString()
  @ValidateIf((o) => o['document_type'] === 'cpf')
  first_name: string

  @IsNotEmpty()
  @IsString()
  @ValidateIf((o) => o['document_type'] === 'cpf')
  last_name: string

  @IsNotEmpty()
  @IsString()
  @IsCNPJOrCPF()
  document: string

  @IsNotEmpty()
  @IsStringNumber()
  @Length(3, 3)
  bank_code: string

  @IsNotEmpty()
  @IsStringNumber()
  @Length(1, 4)
  agency: string

  @IsOptional()
  @IsStringNumber()
  @Length(1, 1)
  agency_digit?: string

  @IsNotEmpty()
  @IsStringNumber()
  @Length(1, 13)
  account: string

  @IsNotEmpty()
  @IsStringNumber()
  @Length(1, 2)
  account_digit: string

  @IsNotEmpty()
  @IsString()
  @IsIn(accountTypeOptions)
  account_type: string

  @IsNotEmpty()
  @IsString()
  @IsIn(documentTypeOptions)
  document_type: string
}
