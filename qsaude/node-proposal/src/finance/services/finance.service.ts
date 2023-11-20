import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { CreateFinanceUuidDto, UpdateFinanceDTO } from '../dtos'
import { FinanceEntity } from '../entities/finance.entity'
import { AbstractFinanceService } from './abstract/finance.service.abstract'

@Injectable()
export class FinanceService implements AbstractFinanceService {
  constructor(
    @InjectRepository(FinanceEntity)
    private readonly financeRepository: Repository<FinanceEntity>
  ) {}

  async findOneBy(filter: Partial<FinanceEntity>): Promise<FinanceEntity> {
    const finance = await this.financeRepository.findOneBy(filter)
    if (!finance) throw new NotFoundException(`Finance was not found`)
    return finance
  }

  async create(createFinanceDTO: CreateFinanceUuidDto): Promise<FinanceEntity> {
    try {
      const finance = this.financeRepository.create(createFinanceDTO)
      return await this.financeRepository.save(finance)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }

  async update(idFinance: string, updateFinanceDto: UpdateFinanceDTO): Promise<FinanceEntity> {
    try {
      const finance = await this.financeRepository.preload({ idFinance, ...updateFinanceDto })
      if (!finance) throw new NotFoundException(`Finance was not found`)

      return await this.financeRepository.save(finance)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }
}
