import { validate, ValidationError } from 'class-validator';
import { plainToInstance } from 'class-transformer';
import { FhirResponse } from '../../fhir/helpers';
import 'reflect-metadata';

const DTOParameterKey = Symbol('DTOParameterKey');

export function Validate(DTOClass: any) {
  return function (
    target: any,
    propertyName: string,
    descriptor: PropertyDescriptor,
  ) {
    const originalMethod = descriptor.value;

    descriptor.value = async function (...args: any[]) {
      const dtoParameterIndex = Reflect.getMetadata(
        DTOParameterKey,
        target,
        propertyName,
      );
      const dtoInstance = plainToInstance(DTOClass, args[dtoParameterIndex]);

      const errors: ValidationError[] = await validate(dtoInstance, {});

      if (errors.length > 0) {
        return FhirResponse.fhirErrorResponse('BAD_REQUEST');
      }

      return originalMethod.apply(this, args);
    };
  };
}

export function ValidateProp() {
  return function (target: any, propertyName: string, parameterIndex: number) {
    Reflect.defineMetadata(
      DTOParameterKey,
      parameterIndex,
      target,
      propertyName,
    );
  };
}
