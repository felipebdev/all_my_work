import { Injectable } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { BaseAbstractRepository } from './base/base.abstract.repository'
import { BankCredentialsEntity } from '@app/common/entities'

@Injectable()
export class BankCredentialsRepository extends BaseAbstractRepository<BankCredentialsEntity> {
  constructor(
    @InjectRepository(BankCredentialsEntity)
    private readonly bankCredentialsRepository: Repository<BankCredentialsEntity>
  ) {
    super(bankCredentialsRepository)
  }
}
