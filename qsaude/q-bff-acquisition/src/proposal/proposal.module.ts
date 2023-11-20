import { Module } from '@nestjs/common'
import { ProposalService } from '@app/proposal/services/proposal.service'
import { ProposalResolver } from '@app/proposal/resolvers/proposal.resolver'
import { PersonModule } from '../person/person.module'
import { TokenModule } from '../token/token.module'

@Module({
  providers: [ProposalService, ProposalResolver],
  imports: [PersonModule, TokenModule]
})
export class ProposalModule {}
