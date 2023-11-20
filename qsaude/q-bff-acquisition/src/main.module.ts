import { ParametrizationModule } from './parametrization/parametrization.module'
import { FileModule } from './file/file.module'
import { Module } from '@nestjs/common'
import { CommonModule } from '@app/common'
import { LeadModule } from './lead/lead.module'
import { PersonModule } from './person/person.module'
import { ProposalModule } from './proposal/proposal.module'
import { TokenModule } from './token/token.module'
import { AddressModule } from './address/address.module'
import { ZipCodeModule } from './zip-code/zip-code.module'
import { CompanyModule } from './company/company.module'
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
    LeadModule,
    PersonModule,
    ProposalModule,
    TokenModule,
    AddressModule,
    FileModule,
    ZipCodeModule,
    CompanyModule,
    FinanceModule,
    ParametrizationModule
  ],
  controllers: [],
  providers: []
})
export class MainModule {}
