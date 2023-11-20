import { IsNotEmpty, IsNumber, IsString } from 'class-validator'
import { CreateFileDto } from './create-file.dto'

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
