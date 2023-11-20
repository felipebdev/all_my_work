import { IsEmail, IsOptional, IsString, IsUUID } from 'class-validator'

class Contact {
  @IsEmail()
  email: string

  @IsString()
  cellphone: string

  @IsUUID()
  refId: string
}

export class ContactUuid extends Contact {
  @IsOptional()
  @IsUUID()
  uuidContact?: string
}
