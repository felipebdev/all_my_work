import IConsumer from '../contracts/consumer'
import IConsumable from '../contracts/consumable'
import BullMqProvider from '../providers/bullmq'

export default class BullMqAdapter implements IConsumer {
  public instance (): IConsumable {
    return BullMqProvider.getInstance()
  }
}
