import { Payload } from '../../job'

export interface IGenerateInvoice {
  bindGenerateInvoice: (payload: Payload) => Promise<void>
}
