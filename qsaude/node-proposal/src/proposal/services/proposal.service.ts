import { CreateProposalUuidDto } from '@app/proposal/dtos'
import { ProposalEntity } from '@app/proposal/entities'
import { AbstractProposalService } from '@app/proposal/services'
import { Injectable, InternalServerErrorException } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class ProposalService implements AbstractProposalService {
  constructor(
    @InjectRepository(ProposalEntity)
    private readonly proposalRepository: Repository<ProposalEntity>,
    private readonly configService: ConfigService
  ) {}

  private async getProposalNumber(): Promise<string> {
    const initialProposalNumber = await this.configService.get<string>('proposal.initialProposalNumber')
    const query = this.proposalRepository.createQueryBuilder('Proposal')
    query.select('MAX(Proposal.ProposalNumber)', 'max')
    const result = await query.getRawOne()
    if (!result.max) {
      return String(initialProposalNumber)
    }
    return String(+result.max + 1)
  }

  async createOrUpdate(proposalDto: CreateProposalUuidDto): Promise<ProposalEntity> {
    try {
      const proposalNumber = await this.getProposalNumber()
      const proposal = this.proposalRepository.create({
        ...proposalDto,
        proposalNumber
      })
      return await this.proposalRepository.save(proposal)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }
}
