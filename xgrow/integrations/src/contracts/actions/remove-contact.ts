import { Payload } from '../../job'

export interface IRemoveContact {
  bindRemoveContact: (payload: Payload) => Promise<void>
}
