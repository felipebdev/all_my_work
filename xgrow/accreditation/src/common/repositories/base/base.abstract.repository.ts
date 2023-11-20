import { DeepPartial, FindManyOptions, FindOneOptions, FindOptionsWhere, Repository } from 'typeorm'

import { BaseInterfaceRepository } from '@app/common/repositories/base/base.interface.repository'

interface HasId {
  id: string
}

export abstract class BaseAbstractRepository<T extends HasId> implements BaseInterfaceRepository<T> {
  private entity: Repository<T>
  protected constructor(entity: Repository<T>) {
    this.entity = entity
  }

  public async save(data: DeepPartial<T>): Promise<T> {
    return this.entity.save(data)
  }
  public async saveMany(data: DeepPartial<T>[]): Promise<T[]> {
    return this.entity.save(data)
  }
  public create(data: DeepPartial<T>): T {
    return this.entity.create(data)
  }
  public createMany(data: DeepPartial<T>[]): T[] {
    return this.entity.create(data)
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  public async findOneById(id: any): Promise<T> {
    const options: FindOptionsWhere<T> = {
      id: id
    }
    return this.entity.findOneBy(options)
  }

  public async findByCondition(filterCondition: FindOneOptions<T>): Promise<T> {
    return this.entity.findOne(filterCondition)
  }

  public async findWithRelations(relations: FindManyOptions<T>): Promise<T[]> {
    return this.entity.find(relations)
  }

  public async findAll(options?: FindManyOptions<T>): Promise<T[]> {
    return this.entity.find(options)
  }

  public async remove(data: T): Promise<T> {
    return this.entity.remove(data)
  }
  public async preload(entityLike: DeepPartial<T>): Promise<T> {
    return this.entity.preload(entityLike)
  }
}
