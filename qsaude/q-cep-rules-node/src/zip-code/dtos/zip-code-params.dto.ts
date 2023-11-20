import { IsString, Length } from 'class-validator'

export class ZipCodeParamsDto {
  @Length(8, 8)
  @IsString()
  zipCode: string
}
