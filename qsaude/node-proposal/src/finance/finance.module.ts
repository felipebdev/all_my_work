import { Module } from '@nestjs/common'
import { FinanceService } from '@app/finance/services'
import { FinanceController } from '@app/finance/controllers'
import { TypeOrmModule } from '@nestjs/typeorm'
import { FinanceEntity } from './entities/finance.entity'
import { AbstractFinanceService } from './services/abstract/finance.service.abstract'

@Module({
  imports: [TypeOrmModule.forFeature([FinanceEntity])],
  providers: [{ provide: AbstractFinanceService, useClass: FinanceService }],
  controllers: [FinanceController]
})
export class FinanceModule {}
