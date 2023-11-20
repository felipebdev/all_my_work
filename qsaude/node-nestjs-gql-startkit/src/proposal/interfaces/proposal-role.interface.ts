import { ProposalRoleEnum } from './enums/proposal.enum'
export interface ProposalRoleInput {
  role: ProposalRoleEnum
  idProposal: string
  idPerson: string
}
