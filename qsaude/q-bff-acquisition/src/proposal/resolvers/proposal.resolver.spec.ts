import { Test, TestingModule } from '@nestjs/testing'
import { ProposalResolver } from './proposal.resolver'
import { ProposalService } from '../services/proposal.service'

describe('ProposalResolver', () => {
  let resolver: ProposalResolver

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        ProposalResolver,
        {
          provide: ProposalService,
          useValue: {
            createLegalRepresentative: jest.fn(() => ({}))
          }
        }
      ]
    }).compile()

    resolver = module.get<ProposalResolver>(ProposalResolver)
  })

  it('should be defined', () => {
    expect(resolver).toBeDefined()
  })
})
