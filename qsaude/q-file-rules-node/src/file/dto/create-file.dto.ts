import { IsNotEmpty, IsString } from 'class-validator'

export class CreateFileDto {
  @IsNotEmpty()
  @IsString()
  fileType: string

  @IsNotEmpty()
  @IsString()
  idPerson: string

  @IsNotEmpty()
  @IsString()
  idProposal: string

  @IsNotEmpty()
  @IsString()
  origin: string
}
