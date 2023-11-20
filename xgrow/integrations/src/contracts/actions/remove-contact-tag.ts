import { Payload } from '../../job'

export interface IRemoveContactTag {
  bindRemoveContactTag: (payload: Payload) => Promise<void>
}
