import { CreatedProposal } from '@app/proposal/models/proposal.model'
import { ProposalService } from '@app/proposal/services/proposal.service'
import { Resolver, Mutation, Args } from '@nestjs/graphql'
import { LegalRepresentativeInput } from '../models/proposal.input.model'

@Resolver()
export class ProposalResolver {
  constructor(private readonly proposalService: ProposalService) {}

  @Mutation(() => CreatedProposal, { nullable: false })
  async createLegalRepresentative(
    @Args('legalRepresentative', { type: () => LegalRepresentativeInput }) legalRep: LegalRepresentativeInput
  ): Promise<CreatedProposal> {
    return this.proposalService.createLegalRepresentative(legalRep) as unknown as CreatedProposal
  }
}
