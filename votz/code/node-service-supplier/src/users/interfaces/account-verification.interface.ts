export enum VERIFICATION_CHANNEL_ENUM {
  EMAIL = 'EMAIL',
  SMS = 'EMAIL',
  WHATSAPP = 'EMAIL',
}

export type VERIFICATION_CHANNEL = 'EMAIL' | 'SMS' | 'WHATSAPP';

export interface VerificationCodeOpts {
  channel: VERIFICATION_CHANNEL;
  name: string;
  code: string;
}
