/* eslint-disable @typescript-eslint/no-explicit-any */
import { ValidCPF, validCNPJ } from '@app/common/validators'
import { registerDecorator, ValidationOptions, ValidationArguments, buildMessage } from 'class-validator'

export function IsCNPJOrCPF(validationOptions?: ValidationOptions) {
  return function (object: Record<string, any>, propertyName: string) {
    registerDecorator({
      name: 'IsCNPJ',
      target: object.constructor,
      propertyName: propertyName,
      options: validationOptions,
      validator: {
        // eslint-disable-next-line @typescript-eslint/no-unused-vars
        validate(value: any, args: ValidationArguments) {
          const validator = {
            cpf: ValidCPF,
            cnpj: validCNPJ
          }
          if (!args.object['document_type']) return false
          return typeof value === 'string' && validator[args.object['document_type']](value)
        },

        defaultMessage: buildMessage(() => `$property must be a valid CNPJ or a valid CPF`, validationOptions)
      }
    })
  }
}
