import { Module } from '@nestjs/common'
import { TypeOrmModule } from '@nestjs/typeorm'
import { ProposalEntity, ProposalRoleEntity } from '@app/proposal/entities'
import { ProposalController, ProposalRoleController } from '@app/proposal/controllers'
import {
  AbstractProposalRoleService,
  AbstractProposalService,
  ProposalRoleService,
  ProposalService
} from '@app/proposal/services'
import { ConfigModule } from '@nestjs/config'
import { proposalConfig } from '@app/proposal/configs'

@Module({
  imports: [TypeOrmModule.forFeature([ProposalEntity, ProposalRoleEntity]), ConfigModule.forFeature(proposalConfig())],
  controllers: [ProposalController, ProposalRoleController],
  providers: [
    { provide: AbstractProposalService, useClass: ProposalService },
    { provide: AbstractProposalRoleService, useClass: ProposalRoleService }
  ],
  exports: [AbstractProposalRoleService]
})
export class ProposalModule {}
