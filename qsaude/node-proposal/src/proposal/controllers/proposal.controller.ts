import { Controller, Post, Body } from '@nestjs/common'
import { PROPOSAL_CONTROLLER } from '@app/proposal/constants'
import { AbstractProposalService } from '@app/proposal/services'
import { CreateProposalUuidDto } from '@app/proposal/dtos'

@Controller(PROPOSAL_CONTROLLER)
export class ProposalController {
  constructor(private readonly proposalService: AbstractProposalService) {}

  @Post()
  async createProposal(@Body() proposalDto: CreateProposalUuidDto) {
    return this.proposalService.createOrUpdate(proposalDto)
  }
}
