import { BadRequestException } from '@nestjs/common'
import { PipeTransform, Injectable, ArgumentMetadata } from '@nestjs/common'
import { MimeType } from './mimetype'

@Injectable()
export class FileValidationPipe implements PipeTransform {
  transform(value: any, metadata: ArgumentMetadata) {
    if (!value) throw new BadRequestException(`Argument 'file' was not provided.`)

    if (value?.size && value.size > parseInt(process.env.S3_FILE_SIZE) * 1024 * 1024)
      throw new BadRequestException('File size is bigger than allowed.')

    if (value?.mimetype && MimeType.includes(value.mimetype) === false)
      throw new BadRequestException('File type is not valid.')

    return value
  }
}
