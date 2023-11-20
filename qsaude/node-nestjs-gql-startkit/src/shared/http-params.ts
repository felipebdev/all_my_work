import { IsUUID } from 'class-validator'

export class UUIDParamsDto {
  @IsUUID()
  id: string
}
