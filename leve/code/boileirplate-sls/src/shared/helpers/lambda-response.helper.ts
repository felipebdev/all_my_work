import { HttpCode, HttpCodeName, LambdaCustomResult } from '../types';

export class LambdaResponse {
  static mount<T>(httpCode: HttpCodeName, data: T): LambdaCustomResult<T> {
    return {
      statusCode: HttpCode[httpCode],
      body: data,
    };
  }
}
