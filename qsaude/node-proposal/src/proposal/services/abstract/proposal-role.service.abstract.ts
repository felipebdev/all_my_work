import { CreateProposalRoleUuidDto } from '@app/proposal/dtos'
import { ProposalRoleEntity } from '@app/proposal/entities'
import { IProposalRoleService } from '@app/proposal/interfaces/services/proposal-role.service.interface'

export abstract class AbstractProposalRoleService implements IProposalRoleService {
  abstract createOrUpdate(proposalDto: CreateProposalRoleUuidDto)
  abstract findOneBy(filter: Partial<ProposalRoleEntity>): Promise<ProposalRoleEntity>
}
