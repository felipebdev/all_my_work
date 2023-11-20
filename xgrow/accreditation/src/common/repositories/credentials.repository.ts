import { Injectable } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { BaseAbstractRepository } from './base/base.abstract.repository'
import { CredentialsEntity } from '@app/common/entities'

@Injectable()
export class CredentialsRepository extends BaseAbstractRepository<CredentialsEntity> {
  constructor(
    @InjectRepository(CredentialsEntity)
    private readonly credentialsRepository: Repository<CredentialsEntity>
  ) {
    super(credentialsRepository)
  }
}
