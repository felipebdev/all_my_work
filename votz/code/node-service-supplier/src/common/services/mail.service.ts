import { Injectable } from '@nestjs/common';
import { MailerService } from '@nestjs-modules/mailer';
import { MailOptions } from '@app/common/interfaces';

@Injectable()
export class MailService {
  constructor(private readonly mailerService: MailerService) {}

  public async sendMail({
    template,
    to,
    options: { context, subject },
  }: MailOptions) {
    await this.mailerService.sendMail({
      from: 'felipebdev@gmail.com',
      to,
      template,
      context,
      subject,
    });
  }
}
