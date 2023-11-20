import { OperationOutcome } from 'fhir/r4';
import {
  HttpCodeDetails,
  HttpCodeDiagnostics,
  HttpCodeFhir,
  HttpCodeName,
  LambdaCustomResult,
  LambdaResponse,
} from '../../shared';

export class FhirResponse extends LambdaResponse {
  static fhirErrorResponse(
    httpCode: HttpCodeName,
  ): LambdaCustomResult<OperationOutcome> {
    return this.mount<OperationOutcome>(httpCode, {
      resourceType: 'OperationOutcome',
      id: '1535b40e-315e-11ee-be56-0242ac120002',
      issue: [
        {
          severity: 'error',
          code: HttpCodeFhir[httpCode],
          details: {
            text: HttpCodeDetails[httpCode],
          },
          diagnostics: HttpCodeDiagnostics[httpCode],
        },
      ],
    });
  }
}
