import { Test, TestingModule } from '@nestjs/testing'
import { getRepositoryToken } from '@nestjs/typeorm'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { InternalServerErrorException } from '@nestjs/common'
import { createMock } from '@golevelup/nestjs-testing'
import { Repository, SelectQueryBuilder } from 'typeorm'
import { ProposalEntity } from '@app/proposal/entities'
import { ProposalService } from '@app/proposal/services'

const proposalMock = {
  idProposal: 'anyanyanyanyany'
}

describe('ProposalService', () => {
  let service: ProposalService
  let proposalRepository: Repository<ProposalEntity>

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 100000005000)
  })

  const PROPOSAL_REPOSITORY_TOKEN = getRepositoryToken(ProposalEntity)

  beforeEach(async () => {
    jest.clearAllTimers()
    jest.clearAllMocks()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        ProposalService,
        {
          provide: PROPOSAL_REPOSITORY_TOKEN,
          useValue: {
            create: jest.fn((args) => args),
            save: jest.fn(() => proposalMock),
            createQueryBuilder: jest.fn(() => ({
              select: jest.fn(),
              getRawOne: jest.fn(() => ({ max: 100000005008 }))
            }))
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<ProposalService>(ProposalService)
    proposalRepository = module.get<Repository<ProposalEntity>>(PROPOSAL_REPOSITORY_TOKEN)
  })

  it('service should be defined', () => {
    expect(service).toBeDefined()
  })

  it('proposalRepository should be defined', () => {
    expect(proposalRepository).toBeDefined()
  })

  describe('createOrUpdate', () => {
    it('should create proposal if nothing fails with previous proposalNumber', async () => {
      const proposal = await service.createOrUpdate({ idLead: '0' })
      expect(proposalRepository.create).toBeCalledTimes(1)
      expect(proposalRepository.createQueryBuilder).toBeCalledTimes(1)
      expect(proposalRepository.create).toBeCalledWith({ idLead: '0', proposalNumber: '100000005009' })
      expect(proposalRepository.save).toBeCalledWith({ idLead: '0', proposalNumber: '100000005009' })
      expect(proposal).toBe(proposalMock)
    })

    it('should create proposal if nothing fails without previous proposalNumber (first proposal db insertion)', async () => {
      jest.spyOn(proposalRepository, 'createQueryBuilder').mockImplementationOnce(
        () =>
          ({
            select: jest.fn(),
            getRawOne: jest.fn(() => ({ max: null }))
          } as unknown as SelectQueryBuilder<ProposalEntity>)
      )
      const proposal = await service.createOrUpdate({ idLead: '0' })
      expect(proposalRepository.create).toBeCalledTimes(1)
      expect(proposalRepository.createQueryBuilder).toBeCalledTimes(1)
      expect(proposalRepository.create).toBeCalledWith({ idLead: '0', proposalNumber: '100000005000' })
      expect(proposalRepository.save).toBeCalledWith({ idLead: '0', proposalNumber: '100000005000' })
      expect(proposal).toBe(proposalMock)
    })

    it('should throw InternalServerErrorException if anything fails', async () => {
      jest.spyOn(proposalRepository, 'save').mockImplementation(() => {
        throw { any: 'error' }
      })
      await expect(service.createOrUpdate({ idLead: '0' })).rejects.toThrowError(
        new InternalServerErrorException({ any: 'error' })
      )
      expect(proposalRepository.create).toBeCalledTimes(1)
      expect(proposalRepository.createQueryBuilder).toBeCalledTimes(1)
      expect(proposalRepository.create).toBeCalledWith({ idLead: '0', proposalNumber: '100000005009' })
      expect(proposalRepository.save).toBeCalledWith({ idLead: '0', proposalNumber: '100000005009' })
    })
  })
})
