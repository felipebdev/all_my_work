/* eslint-disable @typescript-eslint/no-explicit-any */
import { registerDecorator, ValidationOptions, ValidationArguments, buildMessage } from 'class-validator'

export function IsStringNumber(validationOptions?: ValidationOptions) {
  return function (object: Record<string, any>, propertyName: string) {
    registerDecorator({
      name: 'isStringNumber',
      target: object.constructor,
      propertyName: propertyName,
      options: validationOptions,
      validator: {
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
        validate(value: any, args: ValidationArguments) {
          return typeof value === 'string' && !isNaN(Number(value))
        },
        defaultMessage: buildMessage(() => `$property must be a number`, validationOptions)
      }
    })
  }
}
