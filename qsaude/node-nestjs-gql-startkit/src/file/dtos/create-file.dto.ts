import { IsNotEmpty, IsString, IsNumber, IsUUID } from 'class-validator'

export class CreateFileDto {
  @IsNotEmpty()
  @IsString()
  fileType: string

  @IsUUID()
  @IsNotEmpty()
  @IsString()
  idPerson: string

  @IsUUID()
  @IsNotEmpty()
  @IsString()
  idProposal: string
}

export class FileDto extends CreateFileDto {
  @IsNotEmpty()
  @IsString()
  id: string

  @IsNotEmpty()
  @IsString()
  fileUrl: string

  @IsNotEmpty()
  @IsString()
  fileMimetype: string

  @IsNotEmpty()
  @IsString()
  fileOriginalname: string

  @IsNotEmpty()
  @IsNumber()
  fileSize: number
}
