import IConsumable from './contracts/consumable'
import { Job } from './job'

export default abstract class Consumer {
  static init (instance: IConsumable): void {
    instance.consume(
      Job.process,
      Job.resolve,
      Job.reject
    )
  }
}
