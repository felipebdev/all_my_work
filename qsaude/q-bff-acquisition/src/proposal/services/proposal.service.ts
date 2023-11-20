import { ForbiddenException, Injectable, InternalServerErrorException } from '@nestjs/common'
import { PersonService } from '@app/person/services/person.service'
import { HttpService } from '@nestjs/axios'
import { ConfigService } from '@nestjs/config'
import { LegalRepresentativeInput, ProposalInput } from '../models/proposal.input.model'
import { CreatedProposal, Proposal } from '@app/proposal/models/proposal.model'
import { ProposalRole } from '@app/proposal/models/proposal-role.model'
import { ProposalRoleInput } from '@app/proposal/interfaces/proposal-role.interface'
import { ProposalRoleEnum } from '@app/proposal/interfaces/enums/proposal.enum'
import { TokenService } from '@app/token/services/token.service'

@Injectable()
export class ProposalService {
  msBaseUrl: string

  constructor(
    private readonly personService: PersonService,
    private readonly httpService: HttpService,
    private readonly configService: ConfigService,
    private readonly tokenService: TokenService
  ) {
    this.msBaseUrl = this.configService.get<string>('ms.proposal')
  }

  private async create(proposalInput: ProposalInput): Promise<Proposal> {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.post(msBaseUrl.concat('/proposal'), proposalInput)
      return response.data as Proposal
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  private async createProposalRole(proposalRoleInput: ProposalRoleInput): Promise<ProposalRole> {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.post(msBaseUrl.concat('/proposal-role'), proposalRoleInput)
      return response.data as ProposalRole
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async createLegalRepresentative({
    person: personInput,
    proposal: proposalInput,
    tokenValidation: tokenValidationInput
  }: LegalRepresentativeInput): Promise<CreatedProposal> {
    const { email, cellphone, ...personAttrs } = personInput
    const token = true
    // const token = await this.tokenService.check(tokenValidationInput)
    if (!token) {
      throw new ForbiddenException('Provided token is not valid.')
    }
    const person = await this.personService.create(personAttrs)
    const proposal = await this.create(proposalInput)
    const proposalRole = await this.createProposalRole({
      idPerson: person.idPerson,
      idProposal: proposal.idProposal,
      role: ProposalRoleEnum.LegalRepresentative
    })
    await this.personService.createPersonContacts({ refId: proposalRole.idProposalRole, email, cellphone })

    return {
      person: {
        ...person,
        email,
        cellphone
      },
      proposal,
      proposalRole,
      token
    }
  }
}
