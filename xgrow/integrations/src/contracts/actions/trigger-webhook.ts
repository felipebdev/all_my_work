import { Payload } from '../../job'

export interface ITriggerWebhook {
  bindTriggerWebhook: (payload: Payload) => Promise<any>
}
