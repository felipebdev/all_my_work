import { CompanySize } from '@app/company/interfaces'
import { Company, CompanyInput } from '@app/company/models'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { CompanyService } from '@app/company/services'

const companyMock: Company = {
  idCompany: 'any',
  idProposal: 'any',
  cnae: 'any',
  cnpj: 'any',
  codeLegalNature: 'any',
  companySize: CompanySize.ME,
  name: 'any',
  openingDate: 'any',
  tradeName: 'any'
}

const companyInput: CompanyInput = {
  idProposal: 'anyid',
  cnpj: '1234567800001'
}

describe('CompanyService', () => {
  let service: CompanyService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'proposalurl')
  })

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        CompanyService,
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              get: jest.fn(() => ({ data: companyMock })),
              post: jest.fn(() => ({ data: companyMock })),
              patch: jest.fn(() => ({ data: companyMock }))
            }
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<CompanyService>(CompanyService)
    httpService = module.get<HttpService>(HttpService)
  })

  it('providers should be defined', () => {
    expect(service).toBeDefined()
    expect(httpService).toBeDefined()
  })

  describe('getByProposal', () => {
    it('should return company correctly when found', async () => {
      const response = await service.getByProposal('any-id')
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith('proposalurl/company/any-id')
      expect(response).toBe(companyMock)
    })

    it('should throw NotFoundException when Company was not found', async () => {
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
    it('should return created company correctly when found', async () => {
      const response = await service.create(companyInput)
      expect(httpService.axiosRef.post).toBeCalledTimes(1)
      expect(httpService.axiosRef.post).toBeCalledWith('proposalurl/company', companyInput)
      expect(response).toBe(companyMock)
    })

    it('should throw InternalServerErrorException if post fails', async () => {
      jest.spyOn(httpService.axiosRef, 'post').mockImplementation(() => {
        throw { response: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.create(companyInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'post').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.create(companyInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })
  describe('update', () => {
    it('should return updated company correctly when found', async () => {
      const response = await service.update('anyid', companyInput)
      expect(httpService.axiosRef.patch).toBeCalledTimes(1)
      expect(httpService.axiosRef.patch).toBeCalledWith('proposalurl/company/anyid', companyInput)
      expect(response).toBe(companyMock)
    })

    it('should throw InternalServerErrorException if post fails', async () => {
      jest.spyOn(httpService.axiosRef, 'patch').mockImplementation(() => {
        throw { response: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.update('anyid', companyInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'patch').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.update('anyid', companyInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })
})
