import { CreateProposalUuidDto } from '@app/proposal/dtos'
export interface IProposalService {
  createOrUpdate(proposalDto: CreateProposalUuidDto)
}
