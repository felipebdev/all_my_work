export interface MailOptions {
  to: string;
  template: string;
  options?: {
    context?: {
      [key: string]: any;
    };
    subject?: string;
  };
}
