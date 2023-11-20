import { IsString } from 'class-validator';

export class AccountVerificationDto {
  @IsString()
  readonly user: string;

  @IsString()
  readonly code: string;
}
