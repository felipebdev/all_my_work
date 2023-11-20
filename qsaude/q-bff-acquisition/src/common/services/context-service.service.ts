import { CACHE_MANAGER, Inject, Injectable } from '@nestjs/common'
import { Cache } from 'cache-manager'

@Injectable()
export class ContextService {
  constructor(@Inject(CACHE_MANAGER) private readonly cacheManager: Cache | any) {}

  async set<T>(key: string, value: T): Promise<void> {
    await this.cacheManager.set(key, value)
  }

  async get<T>(key: string): Promise<T> {
    return this.cacheManager.get(key)
  }

  async delete(key: string): Promise<void> {
    await this.cacheManager.del(key)
  }
}
