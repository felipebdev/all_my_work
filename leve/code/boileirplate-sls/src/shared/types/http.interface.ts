export type HttpCodeName =
  | 'OK'
  | 'BAD_REQUEST'
  | 'UNAUTHORIZED'
  | 'NOT_FOUND'
  | 'INTERNAL_SERVER_ERROR';

export enum HttpCode {
  OK = 200,
  BAD_REQUEST = 400,
  UNAUTHORIZED = 401,
  NOT_FOUND = 404,
  INTERNAL_SERVER_ERROR = 500,
}

export enum HttpCodeFhir {
  BAD_REQUEST = 'http://hl7.org/fhir/http.code/400',
  UNAUTHORIZED = 'http://hl7.org/fhir/http.code/401',
  NOT_FOUND = 'http://hl7.org/fhir/http.code/404',
  INTERNAL_SERVER_ERROR = 'http://hl7.org/fhir/http.code/500',
}

export enum HttpCodeDetails {
  BAD_REQUEST = 'Bad Request',
  UNAUTHORIZED = 'Unauthorized',
  NOT_FOUND = 'Not Found',
  INTERNAL_SERVER_ERROR = 'Internal Server Error',
}

export enum HttpCodeDiagnostics {
  BAD_REQUEST = 'The request was malformed.',
  UNAUTHORIZED = 'The request was unauthorized.',
  NOT_FOUND = 'The resource was not found.',
  INTERNAL_SERVER_ERROR = 'An unexpected error occured.',
}
