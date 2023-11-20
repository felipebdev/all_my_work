import { Queue } from 'bullmq'

export default interface IConsumable {
  queue: Queue

  consume: (
    callable: CallableFunction,
    resolve: CallableFunction,
    reject: CallableFunction
  ) => void
}
