import { IsCPF } from '@app/shared/validators/cpf'
import { TokenEnum } from '@app/token/interfaces/enum/token.enum'
import { Token } from '@app/token/models/token.model'
import { Field, InputType, OmitType, PartialType, registerEnumType } from '@nestjs/graphql'
import { IsString, IsEnum, IsUUID } from 'class-validator'

registerEnumType(TokenEnum, {
  name: 'TokenEnum',
  description: 'Supported values for token validation type.'
})

@InputType({ description: 'token-input' })
export class CreateTokenInput {
  @IsCPF()
  @Field()
  cpf: string

  @IsEnum(TokenEnum)
  @Field(() => TokenEnum)
  type: TokenEnum

  @IsString()
  @Field()
  value: string

  @IsString()
  @Field()
  name: string
}

@InputType({ description: 'check-token-input' })
export class CheckTokenInput {
  @IsString()
  @Field()
  value: string

  @IsString()
  @Field()
  token: string

  @IsCPF()
  @Field()
  cpf: string

  @IsEnum(TokenEnum)
  @Field(() => TokenEnum)
  type: TokenEnum
}
