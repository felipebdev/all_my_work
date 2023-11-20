import { FinanceFormPayment } from '@app/finance/interfaces/enum/finance.enum'
import { Finance, FinanceInput } from '@app/finance/models'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { FinanceService } from './finance.service'

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

describe('FinanceService', () => {
  let service: FinanceService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'proposalurl')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        FinanceService,
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              get: jest.fn(() => ({ data: financeMock })),
              post: jest.fn(() => ({ data: financeMock })),
              patch: jest.fn(() => ({ data: financeMock }))
            }
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<FinanceService>(FinanceService)
    httpService = module.get<HttpService>(HttpService)
  })

  it('should be defined', () => {
    expect(service).toBeDefined()
    expect(httpService).toBeDefined()
  })

  describe('getByProposal', () => {
    it('should return finance correctly when found', async () => {
      const response = await service.getByProposal('any-id')
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith('proposalurl/finance/any-id')
      expect(response).toBe(financeMock)
    })

    it('should throw NotFoundException when Finance was not found', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 404, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.getByProposal('any')).rejects.toThrowError(new NotFoundException(`ID XYZ WAS NOT FOUND`))
    })

    it('should throw InternalServerErrorException when status error is different than 404', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.getByProposal('any')).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.getByProposal('any')).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })

  describe('create', () => {
    it('should return created finance correctly when found', async () => {
      const response = await service.create(financeInput)
      expect(httpService.axiosRef.post).toBeCalledTimes(1)
      expect(httpService.axiosRef.post).toBeCalledWith('proposalurl/finance', financeInput)
      expect(response).toBe(financeMock)
    })

    it('should throw InternalServerErrorException if post fails', async () => {
      jest.spyOn(httpService.axiosRef, 'post').mockImplementation(() => {
        throw { response: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.create(financeInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'post').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.create(financeInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })

  describe('update', () => {
    it('should return updated finance correctly when found', async () => {
      const response = await service.update('anyid', financeInput)
      expect(httpService.axiosRef.patch).toBeCalledTimes(1)
      expect(httpService.axiosRef.patch).toBeCalledWith('proposalurl/finance/anyid', financeInput)
      expect(response).toBe(financeMock)
    })

    it('should throw InternalServerErrorException if post fails', async () => {
      jest.spyOn(httpService.axiosRef, 'patch').mockImplementation(() => {
        throw { response: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.update('anyid', financeInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'patch').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.update('anyid', financeInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })
})
