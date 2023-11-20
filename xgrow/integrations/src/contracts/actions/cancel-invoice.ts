import { Payload } from '../../job'

export interface ICancelInvoice {
  bindCancelInvoice: (payload: Payload) => Promise<void>
}
