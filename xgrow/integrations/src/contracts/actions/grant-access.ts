import { Payload } from '../../job'

export interface IGrantAccess {
  bindGrantAccess: (payload: Payload) => Promise<void>
}
