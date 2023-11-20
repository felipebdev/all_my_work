import { Injectable } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { BaseAbstractRepository } from './base/base.abstract.repository'
import { AttemptsStepsEntity } from '@app/common/entities'

@Injectable()
export class AttemtpsStepsRepository extends BaseAbstractRepository<AttemptsStepsEntity> {
  constructor(
    @InjectRepository(AttemptsStepsEntity)
    private readonly attemptsStepsRepository: Repository<AttemptsStepsEntity>
  ) {
    super(attemptsStepsRepository)
  }
}
