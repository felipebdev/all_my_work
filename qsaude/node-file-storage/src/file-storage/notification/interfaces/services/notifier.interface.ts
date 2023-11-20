export interface INotifier<Data, Result> {
  notify(topic: string, data: Data): Promise<Result>
}
