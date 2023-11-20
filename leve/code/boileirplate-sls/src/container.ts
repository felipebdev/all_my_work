/* eslint-disable @typescript-eslint/no-unused-vars */
import { Handler, Context, Callback } from 'aws-lambda';
import { HealthController } from './health/controller';

export const healthCheck: Handler = async (
  event: any,
  context: Context,
  callback: Callback,
) => {
  return new HealthController().getHealth(event);
};

export const helloWorld: Handler = async (
  event: any,
  context: Context,
  callback: Callback,
) => {
  return 'Hello World!';
};
