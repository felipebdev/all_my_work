import { Test, TestingModule } from '@nestjs/testing'
import { AbstractProposalService } from '@app/proposal/services'
import { ProposalController } from '@app/proposal/controllers'

const proposalMock = {
  idProposal: 'anyanyanyanyany'
}

describe('ProposalController', () => {
  let controller: ProposalController
  let proposalService: AbstractProposalService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
          provide: AbstractProposalService,
          useValue: {
            createOrUpdate: jest.fn(() => proposalMock)
          }
        }
      ],
      controllers: [ProposalController]
    }).compile()
    controller = module.get<ProposalController>(ProposalController)
    proposalService = module.get<AbstractProposalService>(AbstractProposalService)
  })

  it('controller should be defined', () => {
    expect(controller).toBeDefined()
  })

  it('service should be defined', () => {
    expect(proposalService).toBeDefined()
  })

  describe('createProposal', () => {
    it('should return created proposal when proposalService works correctly', async () => {
      const response = await controller.createProposal({ idLead: '327849237983274' })
      expect(proposalService.createOrUpdate).toBeCalledTimes(1)
      expect(proposalService.createOrUpdate).toBeCalledWith({ idLead: '327849237983274' })
      expect(response).toBe(proposalMock)
    })

    it('throw an error when proposalService fails', async () => {
      jest.spyOn(proposalService, 'createOrUpdate').mockImplementation(() => {
        throw new Error('x')
      })
      await expect(controller.createProposal({ idLead: '327849237983274' })).rejects.toThrowError('x')
      expect(proposalService.createOrUpdate).toBeCalledTimes(1)
      expect(proposalService.createOrUpdate).toBeCalledWith({ idLead: '327849237983274' })
    })
  })
})
