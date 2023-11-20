export interface IProcessable {
  process: () => Promise<void>
}
