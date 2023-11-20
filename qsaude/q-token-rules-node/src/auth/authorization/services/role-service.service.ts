import { Injectable } from '@nestjs/common'
import { AbstractRepository } from '../repositories/abstract-repository.repository'
import { AbstractRoleService } from './abstract-role.service'

@Injectable()
export class RoleService implements AbstractRoleService {
  constructor(private readonly repository: AbstractRepository<string>) {}

  async getRoles(): Promise<string[]> {
    return await this.repository.findAll()
  }
}
