import { IsNotEmpty, IsOptional, IsString, IsUUID, MaxLength, MinLength } from 'class-validator'

export class CreateAddressDto {
  @IsNotEmpty()
  @IsString()
  zipCode: string

  @IsNotEmpty()
  @IsString()
  address: string

  @IsOptional()
  @IsString()
  addressNumber: string

  @IsOptional()
  @IsString()
  addressComplement: string

  @IsNotEmpty()
  @IsString()
  city: string

  @IsNotEmpty()
  @IsString()
  neighborhood: string

  @IsNotEmpty()
  @IsString()
  @MaxLength(2)
  @MinLength(2)
  state: string
}
