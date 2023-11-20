import { Test, TestingModule } from '@nestjs/testing'
import { CreateFinanceUuidDto } from '../dtos'
import { FinanceEntity } from '../entities/finance.entity'
import { FinanceFormPayment } from '../interfaces/enum/finance.enum'
import { AbstractFinanceService } from '../services/abstract/finance.service.abstract'
import { FinanceController } from './finance.controller'

const anyDate = new Date()

const financeMock: FinanceEntity = {
  formPayment: FinanceFormPayment.BOLETO,
  dueDate: '10',
  startingDate: anyDate,
  idProposal: '38f580b6-586b-4af8-bf38-51e0730db6c9',
  idFinance: 'f500a30d-f5a0-4d9c-960f-eb6f125c4305',
  createdAt: anyDate,
  updatedAt: anyDate
}

const financeInput: CreateFinanceUuidDto = {
  formPayment: FinanceFormPayment.BOLETO,
  dueDate: '10',
  startingDate: '1994-07-07',
  idProposal: '38f580b6-586b-4af8-bf38-51e0730db6c9',
  idFinance: 'f500a30d-f5a0-4d9c-960f-eb6f125c4305'
}

describe('FinanceController', () => {
  let controller: FinanceController
  let financeService: AbstractFinanceService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [FinanceController],
      providers: [
        {
          provide: AbstractFinanceService,
          useValue: {
            findOneBy: jest.fn(() => financeMock),
            create: jest.fn(() => financeMock),
            update: jest.fn(() => financeMock)
          }
        }
      ]
    }).compile()

    controller = module.get<FinanceController>(FinanceController)
    financeService = module.get<AbstractFinanceService>(AbstractFinanceService)
  })

  it('controller should be defined', () => {
    expect(controller).toBeDefined()
  })

  it('service should be defined', () => {
    expect(financeService).toBeDefined()
  })

  describe('get', () => {
    it('should return found finance from service', async () => {
      const response = await controller.get({ idProposal: 'anyid' })
      expect(financeService.findOneBy).toBeCalledTimes(1)
      expect(financeService.findOneBy).toBeCalledWith({ idProposal: 'anyid' })
      expect(response).toBe(financeMock)
    })
  })

  describe('create', () => {
    it('should return created finance from service', async () => {
      const response = await controller.create(financeInput)
      expect(financeService.create).toBeCalledTimes(1)
      expect(financeService.create).toBeCalledWith(financeInput)
      expect(response).toBe(financeMock)
    })
  })
  describe('update', () => {
    it('should return updated finance from service', async () => {
      const response = await controller.update({ idFinance: 'anyid' }, financeInput)
      expect(financeService.update).toBeCalledTimes(1)
      expect(financeService.update).toBeCalledWith('anyid', financeInput)
      expect(response).toBe(financeMock)
    })
  })
})
