import { Field, InputType } from '@nestjs/graphql'
import { IsEmail, IsOptional, IsPhoneNumber, IsString, IsUUID } from 'class-validator'

@InputType({ description: 'person' })
export class PersonInput {
  @IsOptional()
  @IsUUID()
  @Field({ nullable: true })
  idPerson?: string

  @IsString()
  @Field()
  name: string

  @IsOptional()
  @IsString()
  @Field({ nullable: true })
  birthday?: string

  @IsOptional()
  @IsString()
  @Field({ nullable: true })
  gender?: string

  @IsOptional()
  @IsString()
  @Field({ nullable: true })
  maritalStatus?: string

  @IsString()
  @Field()
  cpf: string

  @IsOptional()
  @IsString()
  @Field({ nullable: true })
  cns?: string

  @IsOptional()
  @IsString()
  @Field({ nullable: true })
  rg?: string

  @IsOptional()
  @IsString()
  @Field({ nullable: true })
  emittingOrgan?: string

  @IsOptional()
  @IsString()
  @Field({ nullable: true })
  motherName?: string

  @IsEmail()
  @Field()
  email?: string

  @IsPhoneNumber('BR')
  @Field()
  cellphone?: string
}
