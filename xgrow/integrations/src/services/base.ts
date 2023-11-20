// eslint-disable-next-line @typescript-eslint/no-unused-vars
import { ApmSpan } from '../decorators/apm-spam'
import { IProcessable } from '../contracts/processable'
import { IValidation } from '../contracts/validation'
import { Payload } from '../job'

export abstract class BaseService implements IProcessable {
  protected abstract validateSchema
  constructor (
    private readonly validation: IValidation,
    private readonly payload: Payload
  ) {}

  @ApmSpan('validating payload')
  private isValidPayload (): boolean {
    return this.validation.validate(this.validateSchema, this.payload)
  }

  public async process (): Promise<any> {
    if (!this.isValidPayload()) throw new Error('Payload is invalid')
    return await this[this.payload.header.app.action](this.payload)
  }
}
