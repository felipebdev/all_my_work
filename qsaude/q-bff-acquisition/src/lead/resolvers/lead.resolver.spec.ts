import { Test, TestingModule } from '@nestjs/testing'
import { LeadResolver } from './lead.resolver'
import { LeadService } from '../services/lead.service'

const leadMock = {
  uuidLead: 'any',
  numberCPF: 'any',
  completeName: 'José da Silva',
  birthday: '2022-06-10T03:00:00.000Z',
  codePlan: '0030',
  email: 'email@email.com',
  CellPhoneDDD: '11',
  cellPhoneNumber: '987456321',
  tagName: null
}

describe('LeadResolver', () => {
  let resolver: LeadResolver
  let leadService: LeadService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        LeadResolver,
        {
          provide: LeadService,
          useValue: { getLeadById: jest.fn((id) => leadMock) }
        }
      ]
    }).compile()

    resolver = module.get<LeadResolver>(LeadResolver)
    leadService = module.get<LeadService>(LeadService)
  })

  it('resolver should be defined', () => {
    expect(resolver).toBeDefined()
  })

  it('leadService should be defined', () => {
    expect(leadService).toBeDefined()
  })

  describe('findOne', () => {
    it('should return lead correctly when found', async () => {
      const response = await resolver.lead('any')
      expect(leadService.getLeadById).toBeCalledTimes(1)
      expect(leadService.getLeadById).toBeCalledWith('any')
      expect(response).toBe(leadMock)
    })

    it('should throw NotFoundException when Lead was not found', async () => {
      jest.spyOn(leadService, 'getLeadById').mockImplementation(() => {
        throw new Error('x')
      })
      await expect(resolver.lead('any')).rejects.toThrowError('x')
    })
  })
})
