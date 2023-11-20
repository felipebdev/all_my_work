import { Injectable } from '@nestjs/common';
import { CacheService } from '@app/common/services/cache.service';
import { MailService } from '@app/common/services/mail.service';
import { AccountVerificationDto } from '@app/users/dto';
import {
  VerificationCodeOpts,
  VERIFICATION_CHANNEL_ENUM,
} from '../interfaces/account-verification.interface';

@Injectable()
export class AccountService {
  constructor(
    private readonly cacheService: CacheService,
    private readonly mailService: MailService,
  ) {}

  public async sendCodeMail(name: string, code: string) {
    await this.mailService.sendMail({
      to: 'felipebdev@gmail.com',
      template: 'account-verification/html.pug',
      options: {
        context: {
          name,
          code,
        },
        subject: 'Votz - Ative sua conta!',
      },
    });
  }

  public async sendVerificationCode({
    channel,
    name,
    code,
  }: VerificationCodeOpts) {
    switch (channel) {
      case VERIFICATION_CHANNEL_ENUM.EMAIL:
        return this.sendCodeMail(name, code);
      default:
        break;
    }
  }

  public async checkCode(accountVerificationDto: AccountVerificationDto) {
    return;
  }
}
