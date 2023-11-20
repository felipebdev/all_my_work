import { FinanceResolver } from '@app/finance/resolvers'
import { FinanceService } from '@app/finance/services'
import { Module } from '@nestjs/common'

@Module({
  providers: [FinanceService, FinanceResolver]
})
export class FinanceModule {}
