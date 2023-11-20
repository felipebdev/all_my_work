import { Controller, Post, Body } from '@nestjs/common'
import { CreateProposalRoleUuidDto } from '@app/proposal/dtos'
import { AbstractProposalRoleService } from '@app/proposal/services'
import { PROPOSAL_ROLE_CONTROLLER } from '@app/proposal/constants'

@Controller(PROPOSAL_ROLE_CONTROLLER)
export class ProposalRoleController {
  constructor(private readonly proposalRoleService: AbstractProposalRoleService) {}
  @Post()
  async createProposalRole(@Body() proposalRoleDto: CreateProposalRoleUuidDto) {
    return this.proposalRoleService.createOrUpdate(proposalRoleDto)
  }
}
