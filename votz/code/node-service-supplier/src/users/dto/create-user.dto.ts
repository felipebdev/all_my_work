import { IsString, IsEmail, IsPhoneNumber } from 'class-validator';

export class CreateUserDto {
  @IsString()
  readonly first_name: string;

  @IsString()
  readonly last_name: string;

  @IsEmail()
  readonly email: string;

  @IsPhoneNumber('BR')
  readonly phone: string;

  @IsString()
  readonly password: string;
}
