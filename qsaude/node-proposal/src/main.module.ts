import { CommonModule } from '@app/common/common.module'
import { Module } from '@nestjs/common'
import { AddressModule } from '@app/address/address.module'
import { ContactModule } from '@app/contact/contact.module'
import { ProposalModule } from '@app/proposal/proposal.module'
import { PersonModule } from '@app/person/person.module'
import { CompanyModule } from '@app/company/company.module'
import { FinanceModule } from './finance/finance.module'

@Module({
  imports: [
    CommonModule.register({
      configModule: {
        ignoreEnvFile: ['production', 'staging'].includes(process.env.NODE_ENV),
        envFilePath: '.env',
        expandVariables: ['development', 'test'].includes(process.env.NODE_ENV),
        cache: ['production', 'staging'].includes(process.env.NODE_ENV),
        isGlobal: true
      }
    }),
    AddressModule,
    ContactModule,
    ProposalModule,
    PersonModule,
    CompanyModule,
    FinanceModule
  ],
  controllers: [],
  providers: []
})
export class MainModule {}
