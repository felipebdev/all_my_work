/* eslint-disable @typescript-eslint/no-unused-vars */
import { Handler, Context, Callback, APIGatewayProxyResult } from 'aws-lambda';
import { UserController } from './user/controller/user.controller';
import { UsernameDTO } from './user/dto';

export const getUser: Handler = async (
  { queryStringParameters }: any,
  context: Context,
  callback: Callback,
): Promise<APIGatewayProxyResult> => {
  const { body, statusCode } = await new UserController().getUser(
    queryStringParameters as UsernameDTO,
  );
  return {
    statusCode,
    body: JSON.stringify(body),
  };
};
