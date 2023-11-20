import { CreateProposalRoleUuidDto } from '@app/proposal/dtos'
import { ProposalRoleEntity } from '@app/proposal/entities'
export interface IProposalRoleService {
  createOrUpdate(proposalDto: CreateProposalRoleUuidDto)
  findOneBy(filter: Partial<ProposalRoleEntity>)
}
