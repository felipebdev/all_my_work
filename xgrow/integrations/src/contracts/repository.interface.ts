export interface IRepository<T> {
  create: (entity: any) => Promise<void>
  update: (id: string, entity: any) => Promise<void>
  delete: (id: string) => Promise<void>
  findById: (id: string) => Promise<T>
}
