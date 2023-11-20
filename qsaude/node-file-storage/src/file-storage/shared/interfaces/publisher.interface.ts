export interface IPublisher {
  publish<Data>(topic: string, data: Data): Promise<void>
}
