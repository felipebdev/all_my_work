interface CustomErrorOptions {
  code: number;
  details?: any;
}

export class CustomError extends Error {
  code: number;
  details?: any;

  constructor(message: string, options: CustomErrorOptions) {
    super(message);
    this.name = 'CustomError';
    this.code = options.code;
    this.details = options.details;
  }
}
