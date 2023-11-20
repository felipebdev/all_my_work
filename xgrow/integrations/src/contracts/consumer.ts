import IConsumable from './consumable'

export default interface IConsumer {
  instance: () => IConsumable
}
