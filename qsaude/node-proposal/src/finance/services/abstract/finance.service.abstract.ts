import { IFinanceService } from '@app/finance/interfaces/services/finance.service.interface'
import { FinanceEntity } from '@app/finance/entities/finance.entity'
import { CreateFinanceUuidDto, UpdateFinanceDTO } from '@app/finance/dtos'

export abstract class AbstractFinanceService implements IFinanceService {
  abstract findOneBy(filter: Partial<FinanceEntity>): Promise<FinanceEntity>
  abstract create(createFinanceDTO: CreateFinanceUuidDto): Promise<FinanceEntity>
  abstract update(idFinance: string, updateFinanceDto: UpdateFinanceDTO): Promise<FinanceEntity>
}
