import { IsNotEmpty, IsUUID } from 'class-validator'
import { CreateAddressDto } from './create-address.dto'
import { PartialType } from '@nestjs/swagger'

export class CreatePersonAddressDto extends CreateAddressDto {}

export class CreateCompanyAddressDto extends CreateAddressDto {
  @IsNotEmpty()
  @IsUUID()
  idCompany: string
}

export class UpdateAddressDto extends PartialType(CreateAddressDto) {}
