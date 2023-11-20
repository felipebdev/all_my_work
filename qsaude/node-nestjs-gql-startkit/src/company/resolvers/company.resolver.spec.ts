import { CompanySize } from '@app/company/interfaces'
import { Company, CompanyInput } from '@app/company/models'
import { CompanyService } from '@app/company/services'
import { Test, TestingModule } from '@nestjs/testing'
import { CompanyResolver } from './company.resolver'

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

describe('CompanyResolver', () => {
  let resolver: CompanyResolver
  let service: CompanyService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        CompanyResolver,
        {
          provide: CompanyService,
          useValue: {
            getByProposal: jest.fn(() => companyMock),
            create: jest.fn(() => companyMock),
            update: jest.fn(() => companyMock)
          }
        }
      ]
    }).compile()

    resolver = module.get<CompanyResolver>(CompanyResolver)
    service = module.get<CompanyService>(CompanyService)
  })

  it('providers should be defined', () => {
    expect(resolver).toBeDefined()
    expect(service).toBeDefined()
  })

  describe('companyByProposal', () => {
    it('should return company correctly when found', async () => {
      const response = await resolver.company('any')
      expect(service.getByProposal).toBeCalledTimes(1)
      expect(service.getByProposal).toBeCalledWith('any')
      expect(response).toBe(companyMock)
    })
  })
  describe('createCompany', () => {
    it('should return created company correctly', async () => {
      const response = await resolver.createCompany(companyInput)
      expect(service.create).toBeCalledTimes(1)
      expect(service.create).toBeCalledWith(companyInput)
      expect(response).toBe(companyMock)
    })
  })
  describe('updateCompany', () => {
    it('should return updated company correctly', async () => {
      const response = await resolver.updateCompany('anyid', companyInput)
      expect(service.update).toBeCalledTimes(1)
      expect(service.update).toBeCalledWith('anyid', companyInput)
      expect(response).toBe(companyMock)
    })
  })
})
