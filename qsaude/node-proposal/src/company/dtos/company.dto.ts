import { CompanySize } from '@app/company/interfaces'
import { IsCNPJ } from '@app/shared/validators/cnpj'
import { IsOnlyDate } from '@app/shared/validators/date'
import { PartialType } from '@nestjs/swagger'
import { Transform, Type } from 'class-transformer'
import {
  IsString,
  IsOptional,
  IsUUID,
  ValidateNested,
  IsNotEmptyObject,
  IsEnum,
  MaxLength,
  IsNotEmpty
} from 'class-validator'

import moment from 'moment'
export class CreateCompanyDTO {
  @IsCNPJ()
  @Transform(({ value }) => String(value).replace(/[^a-zA-Z0-9 ]/g, ''))
  @IsOptional()
  cnpj: string

  @IsString()
  @IsNotEmpty()
  @IsOptional()
  name: string

  @IsString()
  @IsNotEmpty()
  @IsOptional()
  tradeName: string

  @MaxLength(5)
  @IsString()
  @IsNotEmpty()
  @IsOptional()
  codeLegalNature: string

  @IsString()
  @IsEnum(CompanySize)
  @IsNotEmpty()
  @IsOptional()
  companySize: CompanySize

  @MaxLength(8)
  @IsString()
  @IsNotEmpty()
  @IsOptional()
  cnae: string

  @IsOnlyDate()
  @Transform(({ value }) => moment(value).format('YYYY-MM-DD'), {
    toClassOnly: true
  })
  @IsOptional()
  openingDate: string

  @IsUUID()
  @IsNotEmpty()
  idProposal: string
}

export class CreateCompanyUuidDto extends CreateCompanyDTO {
  @IsOptional()
  @IsUUID()
  idCompany: string
}

export class BaseCreateCompanyUuidDTO {
  @IsNotEmptyObject()
  @ValidateNested({ each: true })
  @Type(() => CreateCompanyUuidDto)
  company: CreateCompanyUuidDto
}

export class GetCompanyParamsDto {
  @IsUUID()
  idProposal: string
}

export class PatchCompanyParamsDto {
  @IsUUID()
  idCompany: string
}

export class UpdateCompanyDto extends PartialType(CreateCompanyUuidDto) {}
