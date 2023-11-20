import { FinanceFormPayment } from '@app/finance/interfaces/enum/finance.enum'
import { Finance, FinanceInput } from '@app/finance/models'
import { FinanceService } from '@app/finance/services'
import { Test, TestingModule } from '@nestjs/testing'
import { FinanceResolver } from './finance.resolver'

const financeMock: Finance = {
  idFinance: 'any',
  idProposal: 'any',
  formPayment: FinanceFormPayment.BOLETO,
  dueDate: 'any',
  startingDate: 'any'
}

const financeInput: FinanceInput = {
  idProposal: 'anyid',
  formPayment: FinanceFormPayment.BOLETO
}

describe('FinanceResolver', () => {
  let resolver: FinanceResolver
  let service: FinanceService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        FinanceResolver,
        {
          provide: FinanceService,
          useValue: {
            getByProposal: jest.fn(() => financeMock),
            create: jest.fn(() => financeMock),
            update: jest.fn(() => financeMock)
          }
        }
      ]
    }).compile()

    resolver = module.get<FinanceResolver>(FinanceResolver)
    service = module.get<FinanceService>(FinanceService)
  })

  it('should be defined', () => {
    expect(resolver).toBeDefined()
    expect(service).toBeDefined()
  })

  describe('financeByProposal', () => {
    it('should return finance correctly when found', async () => {
      const response = await resolver.finance('any')
      expect(service.getByProposal).toBeCalledTimes(1)
      expect(service.getByProposal).toBeCalledWith('any')
      expect(response).toBe(financeMock)
    })
  })
  describe('createFinance', () => {
    it('should return created finance correctly', async () => {
      const response = await resolver.createFinance(financeInput)
      expect(service.create).toBeCalledTimes(1)
      expect(service.create).toBeCalledWith(financeInput)
      expect(response).toBe(financeMock)
    })
  })
  describe('updateFinance', () => {
    it('should return updated finance correctly', async () => {
      const response = await resolver.updateFinance('anyid', financeInput)
      expect(service.update).toBeCalledTimes(1)
      expect(service.update).toBeCalledWith('anyid', financeInput)
      expect(response).toBe(financeMock)
    })
  })
})
