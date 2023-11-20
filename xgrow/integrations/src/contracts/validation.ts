import { Payload } from '../job'

export interface IValidation {
  validate: (schema: any, data: Payload) => boolean
}
