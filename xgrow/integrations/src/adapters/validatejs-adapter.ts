import { IValidation } from '../contracts/validation'
import { Payload } from '../job'
import { nestedValue } from '../utils/helper'

/**
 * @see https://stackoverflow.com/questions/38616612/javascript-elegant-way-to-check-object-has-required-properties
 */
export class ValidateJsAdapter implements IValidation {
  public validate (schema: object, data: Payload): boolean {
    const errors = Object.keys(schema)
      .filter(key => !schema[key](nestedValue(data, key)))
      .map(key => `Payload ${key} is invalid`)

    return (errors.length <= 0)
  }
}
