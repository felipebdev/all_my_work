import { Module } from '@nestjs/common'
import { AbstractCompanyService, CompanyService } from '@app/company/services'
import { CompanyController } from '@app/company/controllers'
import { TypeOrmModule } from '@nestjs/typeorm'
import { CompanyEntity } from '@app/company/entities'

@Module({
  imports: [TypeOrmModule.forFeature([CompanyEntity])],
  providers: [{ provide: AbstractCompanyService, useClass: CompanyService }],
  controllers: [CompanyController]
})
export class CompanyModule {}
