import { Payload } from '../../job'

export interface IInsertContact {
  bindInsertContact: (payload: Payload) => Promise<void>
}
