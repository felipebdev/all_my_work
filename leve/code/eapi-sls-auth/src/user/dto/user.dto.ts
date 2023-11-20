import { IsString } from 'class-validator';
import { IsCPF } from '../../shared';

export class UsernameDTO {
  @IsString()
  @IsCPF()
  username: string;
}
