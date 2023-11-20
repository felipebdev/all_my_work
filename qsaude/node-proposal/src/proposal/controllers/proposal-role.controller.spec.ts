import { Test, TestingModule } from '@nestjs/testing'
import { AbstractProposalRoleService } from '@app/proposal/services'
import { ProposalRoleController } from '@app/proposal/controllers'
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

describe('ProposalRoleController', () => {
  let controller: ProposalRoleController
  let proposalRoleService: AbstractProposalRoleService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
          provide: AbstractProposalRoleService,
          useValue: {
            createOrUpdate: jest.fn(() => proposalRoleMock)
          }
        }
      ],
      controllers: [ProposalRoleController]
    }).compile()
    controller = module.get<ProposalRoleController>(ProposalRoleController)
    proposalRoleService = module.get<AbstractProposalRoleService>(AbstractProposalRoleService)
  })

  it('controller should be defined', () => {
    expect(controller).toBeDefined()
  })

  it('service should be defined', () => {
    expect(proposalRoleService).toBeDefined()
  })

  describe('createProposal', () => {
    it('should return created proposal when proposalRoleService works correctly', async () => {
      const response = await controller.createProposalRole(proposalRoleInput)
      expect(proposalRoleService.createOrUpdate).toBeCalledTimes(1)
      expect(proposalRoleService.createOrUpdate).toBeCalledWith(proposalRoleInput)
      expect(response).toBe(proposalRoleMock)
    })

    it('throw an error when proposalService fails', async () => {
      jest.spyOn(proposalRoleService, 'createOrUpdate').mockImplementation(() => {
        throw new Error('x')
      })
      await expect(controller.createProposalRole(proposalRoleInput)).rejects.toThrowError('x')
      expect(proposalRoleService.createOrUpdate).toBeCalledTimes(1)
      expect(proposalRoleService.createOrUpdate).toBeCalledWith(proposalRoleInput)
    })
  })
})
