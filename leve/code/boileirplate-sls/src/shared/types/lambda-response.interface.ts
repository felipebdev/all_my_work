import { APIGatewayProxyResult } from 'aws-lambda';

export interface LambdaCustomResult<T = any>
  extends Omit<APIGatewayProxyResult, 'body'> {
  body: T;
}
