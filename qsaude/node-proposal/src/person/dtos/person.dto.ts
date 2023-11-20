import { Gender } from '@app/person/interfaces/enums'
import { IsCPF } from '@app/shared/validators/cpf'
import { IsOnlyDate } from '@app/shared/validators/date'
import { PartialType } from '@nestjs/swagger'
import { Transform, Type } from 'class-transformer'
import {
  IsNotEmpty,
  IsString,
  IsOptional,
  IsIn,
  IsUUID,
  ValidateNested,
  IsNotEmptyObject,
  IsEnum
} from 'class-validator'

import moment from 'moment'

export const maritalStatus = ['C', 'D', 'M', 'Q', 'S', 'V', 'B']

export class CreatePersonDto {
  @IsString()
  @IsOptional()
  name: string

  @IsOptional()
  @IsString()
  socialName: string

  @IsOptional()
  @IsOnlyDate()
  @Transform(({ value }) => moment(value).format('YYYY-MM-DD'), {
    toClassOnly: true
  })
  birthday: string

  @IsOptional()
  @IsString()
  @IsEnum(Gender)
  gender: Gender

  @IsIn(maritalStatus)
  @IsOptional()
  maritalStatus: string

  @IsOptional()
  @IsCPF()
  cpf: string

  @IsString()
  @IsOptional()
  cns: string

  @IsString()
  @IsOptional()
  rg: string

  @IsString()
  @IsOptional()
  emittingOrgan: string

  @IsString()
  @IsOptional()
  motherName: string
}

export class CreatePersonUuidDto extends CreatePersonDto {
  @IsOptional()
  @IsUUID()
  idPerson: string
}

export class UpdatePersonDto extends PartialType(CreatePersonUuidDto) {}

export class BaseCreateUuidDTO {
  @IsNotEmptyObject()
  @ValidateNested({ each: true })
  @Type(() => CreatePersonUuidDto)
  person: CreatePersonUuidDto
}
