import { ApiProperty } from '@nestjs/swagger'

export class HandlePostFileDto {
  @ApiProperty({ type: 'string', format: 'binary' })
  file: any
}
