import { CreateFinanceUuidDto, UpdateFinanceDTO } from '@app/finance/dtos'
import { FinanceEntity } from '@app/finance/entities/finance.entity'

export interface IFinanceService {
  findOneBy(filter: Partial<FinanceEntity>): Promise<FinanceEntity>
  create(createFinanceDTO: CreateFinanceUuidDto): Promise<FinanceEntity>
  update(idFinance: string, updateFinanceDto: UpdateFinanceDTO): Promise<FinanceEntity>
}
