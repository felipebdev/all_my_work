import { Module } from '@nestjs/common'
import { CompanyResolver } from '@app/company/resolvers'
import { CompanyService } from '@app/company/services'

@Module({
  providers: [CompanyResolver, CompanyService]
})
export class CompanyModule {}
