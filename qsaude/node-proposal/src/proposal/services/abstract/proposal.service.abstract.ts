import { CreateProposalUuidDto } from '@app/proposal/dtos'
import { IProposalService } from '@app/proposal/interfaces/services/proposal.service.interface'

export abstract class AbstractProposalService implements IProposalService {
  abstract createOrUpdate(proposalDto: CreateProposalUuidDto)
}
