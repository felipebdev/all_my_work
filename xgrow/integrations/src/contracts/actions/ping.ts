import { Payload } from '../../job'

export interface IPing {
  ping: (payload: Payload) => Promise<void>
}
