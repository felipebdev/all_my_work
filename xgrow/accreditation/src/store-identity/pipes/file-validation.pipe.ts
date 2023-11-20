import { BadRequestException } from '@nestjs/common'
import { PipeTransform, Injectable, ArgumentMetadata } from '@nestjs/common'
import { MimeType } from '@app/store-identity/pipes'

@Injectable()
export class FileValidationPipe implements PipeTransform {
  // eslint-disable-next-line @typescript-eslint/no-unused-vars, @typescript-eslint/no-explicit-any
  transform(value: any, metadata: ArgumentMetadata) {
    if (!value) throw new BadRequestException(`Argument 'file' was not provided.`)

    if (value?.mimetype && MimeType.includes(value.mimetype) === false)
      throw new BadRequestException('File type is not valid.')

    return value
  }
}
