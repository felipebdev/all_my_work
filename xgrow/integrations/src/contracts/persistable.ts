export interface IPersistable {
  connect: () => Promise<any>
  disconnect: () => Promise<void>
}
