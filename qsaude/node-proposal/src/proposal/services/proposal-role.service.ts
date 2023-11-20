import { CreateProposalRoleUuidDto } from '@app/proposal/dtos'
import { AbstractProposalRoleService } from '@app/proposal/services'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { ProposalRoleEntity } from '../entities/proposal-role.entity'
import { Repository } from 'typeorm'

@Injectable()
export class ProposalRoleService implements AbstractProposalRoleService {
  constructor(
    @InjectRepository(ProposalRoleEntity)
    private readonly proposalRoleRepository: Repository<ProposalRoleEntity>
  ) {}

  async findOneBy(filter: Partial<ProposalRoleEntity>) {
    const proposalRole = await this.proposalRoleRepository.findOneBy(filter)
    if (!proposalRole) throw new NotFoundException('ProposalRole was not found')
    return proposalRole
  }

  async createOrUpdate(proposalDto: CreateProposalRoleUuidDto) {
    try {
      const proposalRole = this.proposalRoleRepository.create(proposalDto)
      return await this.proposalRoleRepository.save(proposalRole)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }
}
