import { IFind } from '../interfaces/repositories/find.interface'

export abstract class AbstractRepository<T> implements IFind {
  abstract findAll(): Promise<T[]>
}
