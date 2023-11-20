import { Payload } from '../../job'

export interface IInsertContactTag {
  bindInsertContactTag: (payload: Payload) => Promise<void>
}
