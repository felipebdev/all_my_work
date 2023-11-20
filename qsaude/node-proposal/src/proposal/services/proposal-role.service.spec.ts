import { Test, TestingModule } from '@nestjs/testing'
import { getRepositoryToken } from '@nestjs/typeorm'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { InternalServerErrorException } from '@nestjs/common'
import { createMock } from '@golevelup/nestjs-testing'
import { Repository } from 'typeorm'
import { ProposalRoleEntity } from '@app/proposal/entities'
import { ProposalRoleService } from '@app/proposal/services'
import { CreateProposalRoleUuidDto } from '@app/proposal/dtos'

const proposalRoleMock = {
  role: 'LEGAL_REPRESENTATIVE',
  proposal: '10b564f7-b450-4511-a2b1-2923777a84e8',
  person: '356108cf-29ed-49fc-8039-23fe4cc2ca11',
  idProposalRole: 'fa66a211-4132-4e19-b70a-e0bc196b7ca3',
  createdAt: '2022-09-20T13:39:34.000Z',
  updatedAt: '2022-09-20T13:39:34.000Z'
}

const proposalRoleInput: CreateProposalRoleUuidDto = {
  idProposalRole: 'anyid',
  idPerson: 'anyperson',
  idProposal: 'anyproposal',
  role: 'LEGAL_RESPONSIBLE'
}

describe('ProposalRoleService', () => {
  let service: ProposalRoleService
  let proposalRoleRepository: Repository<ProposalRoleEntity>

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 100000005000)
  })

  const PROPOSAL_ROLE_REPOSITORY_TOKEN = getRepositoryToken(ProposalRoleEntity)

  beforeEach(async () => {
    jest.clearAllTimers()
    jest.clearAllMocks()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        ProposalRoleService,
        {
          provide: PROPOSAL_ROLE_REPOSITORY_TOKEN,
          useValue: {
            create: jest.fn((args) => args),
            save: jest.fn(() => proposalRoleMock)
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<ProposalRoleService>(ProposalRoleService)
    proposalRoleRepository = module.get<Repository<ProposalRoleEntity>>(PROPOSAL_ROLE_REPOSITORY_TOKEN)
  })

  it('service should be defined', () => {
    expect(service).toBeDefined()
  })

  it('proposalRoleRepository should be defined', () => {
    expect(proposalRoleRepository).toBeDefined()
  })

  describe('createOrUpdate', () => {
    it('should create proposalRole if nothing fails', async () => {
      const proposalRole = await service.createOrUpdate(proposalRoleInput)
      expect(proposalRoleRepository.create).toBeCalledTimes(1)
      expect(proposalRoleRepository.save).toBeCalledTimes(1)
      expect(proposalRoleRepository.create).toBeCalledWith(proposalRoleInput)
      expect(proposalRoleRepository.save).toBeCalledWith(proposalRoleInput)
      expect(proposalRole).toBe(proposalRoleMock)
    })

    it('should throw InternalServerErrorException if anything fails', async () => {
      jest.spyOn(proposalRoleRepository, 'save').mockImplementation(() => {
        throw { any: 'error' }
      })
      await expect(service.createOrUpdate(proposalRoleInput)).rejects.toThrowError(
        new InternalServerErrorException({ any: 'error' })
      )
      expect(proposalRoleRepository.create).toBeCalledTimes(1)
      expect(proposalRoleRepository.save).toBeCalledTimes(1)
      expect(proposalRoleRepository.create).toBeCalledWith(proposalRoleInput)
      expect(proposalRoleRepository.save).toBeCalledWith(proposalRoleInput)
    })
  })
})
