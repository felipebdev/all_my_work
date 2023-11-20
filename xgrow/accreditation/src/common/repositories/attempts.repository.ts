import { Injectable } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { AttemptEntity } from '../entities/attempts.entity'
import { BaseAbstractRepository } from './base/base.abstract.repository'

@Injectable()
export class AttemtpsRepository extends BaseAbstractRepository<AttemptEntity> {
  constructor(
    @InjectRepository(AttemptEntity)
    private readonly attemptsRepository: Repository<AttemptEntity>
  ) {
    super(attemptsRepository)
  }
}
