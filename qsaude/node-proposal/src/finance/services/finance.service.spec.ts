import { InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { Test, TestingModule } from '@nestjs/testing'
import { getRepositoryToken } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { CreateFinanceUuidDto } from '../dtos'
import { FinanceEntity } from '../entities/finance.entity'
import { FinanceFormPayment } from '../interfaces/enum/finance.enum'
import { FinanceService } from './finance.service'

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

describe('FinanceService', () => {
  let service: FinanceService
  let financeRepository: Repository<FinanceEntity>

  const FINANCE_REPOSITORY_TOKEN = getRepositoryToken(FinanceEntity)

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        FinanceService,
        {
          provide: FINANCE_REPOSITORY_TOKEN,
          useValue: {
            findOneBy: jest.fn(() => financeMock),
            create: jest.fn((args) => ({ ...args })),
            save: jest.fn(() => financeMock),
            preload: jest.fn((args) => ({ ...args })),
            delete: jest.fn(() => ({}))
          }
        }
      ]
    }).compile()

    service = module.get<FinanceService>(FinanceService)
    financeRepository = module.get<Repository<FinanceEntity>>(FINANCE_REPOSITORY_TOKEN)
  })

  it('should be defined', () => {
    expect(service).toBeDefined()
  })

  it('financeRepository should be defined', () => {
    expect(financeRepository).toBeDefined()
  })

  describe('findOneBy', () => {
    it('should return finance when when found', async () => {
      const response = await service.findOneBy({ idFinance: '12345678910' })
      expect(financeRepository.findOneBy).toBeCalledTimes(1)
      expect(financeRepository.findOneBy).toBeCalledWith({ idFinance: '12345678910' })
      expect(response).toBe(financeMock)
    })

    it('should throw NotFoundException when finance was not found', async () => {
      jest.spyOn(financeRepository, 'findOneBy').mockReturnValue(null)
      await expect(service.findOneBy({ idFinance: 'any' })).rejects.toThrowError(
        new NotFoundException(`Finance was not found`)
      )
      expect(financeRepository.findOneBy).toBeCalledTimes(1)
      expect(financeRepository.findOneBy).toBeCalledWith({
        idFinance: 'any'
      })
    })
  })

  describe('create', () => {
    it('should use financeRepository .create and .save correctly', async () => {
      const finance = await service.create(financeInput)
      expect(financeRepository.create).toBeCalledTimes(1)
      expect(financeRepository.save).toBeCalledTimes(1)
      expect(financeRepository.create).toBeCalledWith(financeInput)
      expect(financeRepository.save).toBeCalledWith(financeInput)
      expect(finance).toBe(financeMock)
    })

    it('should throw InternalServerErrorException if .save or .create fails', async () => {
      jest.spyOn(financeRepository, 'create').mockImplementationOnce(() => {
        throw { any: 'any' }
      })
      await expect(service.create(financeInput)).rejects.toThrowError(new InternalServerErrorException({ any: 'any' }))
      expect(financeRepository.create).toBeCalledTimes(1)
      expect(financeRepository.create).toBeCalledWith(financeInput)
      expect(financeRepository.save).toBeCalledTimes(0)
    })
  })

  describe('update', () => {
    it('should return finance correctly when updated', async () => {
      const response = await service.update('anyid', { dueDate: '20' })
      expect(financeRepository.preload).toBeCalledTimes(1)
      expect(financeRepository.preload).toBeCalledWith({ idFinance: 'anyid', dueDate: '20' })
      expect(financeRepository.save).toBeCalledTimes(1)
      expect(financeRepository.save).toBeCalledWith({
        idFinance: 'anyid',
        dueDate: '20'
      })
      expect(response).toBe(financeMock)
    })

    it('should throw InternalServerErrorException when any method fails', async () => {
      jest.spyOn(financeRepository, 'preload').mockImplementationOnce(() => {
        throw { any: 'any' }
      })
      await expect(service.update('anyid', { dueDate: '20' })).rejects.toThrowError(
        new InternalServerErrorException({ any: 'any' })
      )
      expect(financeRepository.preload).toBeCalledTimes(1)
      expect(financeRepository.preload).toBeCalledWith({ idFinance: 'anyid', dueDate: '20' })
      expect(financeRepository.save).toBeCalledTimes(0)
    })
  })
})
