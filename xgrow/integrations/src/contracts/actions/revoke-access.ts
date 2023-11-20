import { Payload } from '../../job'

export interface IRevokeAccess {
  bindRevokeAccess: (payload: Payload) => Promise<void>
}
