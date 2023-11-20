import { CompanyEntity } from '@app/company/entities'
import { CompanySize } from '@app/company/interfaces'
import { Test, TestingModule } from '@nestjs/testing'
import { CompanyService } from '@app/company/services'
import { getRepositoryToken } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { CreateCompanyUuidDto } from '../dtos/company.dto'

const anyDate = new Date()

const companyMock: CompanyEntity = {
  cnpj: '25180906000166',
  name: 'EMPRESA X LTDA',
  tradeName: 'Emprexa X',
  codeLegalNature: '1234',
  cnae: '1234567',
  companySize: CompanySize.ME,
  openingDate: anyDate,
  idProposal: '38f580b6-586b-4af8-bf38-51e0730db6c9',
  idCompany: 'd05af941-faaa-484e-aa53-8516b5a9c212',
  createdAt: anyDate,
  updatedAt: anyDate
}

const companyInput: CreateCompanyUuidDto = {
  cnpj: '25180906000166',
  name: 'EMPRESA X LTDA',
  tradeName: 'Emprexa X',
  codeLegalNature: '1234',
  cnae: '1234567',
  companySize: CompanySize.ME,
  openingDate: '1994-07-07',
  idProposal: '38f580b6-586b-4af8-bf38-51e0730db6c9',
  idCompany: 'd05af941-faaa-484e-aa53-8516b5a9c212'
}

describe('CompanyService', () => {
  let service: CompanyService
  let companyRepository: Repository<CompanyEntity>

  const COMPANY_REPOSITORY_TOKEN = getRepositoryToken(CompanyEntity)

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        CompanyService,
        {
          provide: COMPANY_REPOSITORY_TOKEN,
          useValue: {
            findOneBy: jest.fn(() => companyMock),
            create: jest.fn((args) => ({ ...args })),
            save: jest.fn(() => companyMock),
            preload: jest.fn((args) => ({ ...args })),
            delete: jest.fn(() => ({}))
          }
        }
      ]
    }).compile()

    service = module.get<CompanyService>(CompanyService)
    companyRepository = module.get<Repository<CompanyEntity>>(COMPANY_REPOSITORY_TOKEN)
  })

  it('service should be defined', () => {
    expect(service).toBeDefined()
  })

  it('companyRepository should be defined', () => {
    expect(companyRepository).toBeDefined()
  })

  describe('findOneBy', () => {
    it('should return company when when found', async () => {
      const response = await service.findOneBy({ cnpj: '12345678910' })
      expect(companyRepository.findOneBy).toBeCalledTimes(1)
      expect(companyRepository.findOneBy).toBeCalledWith({ cnpj: '12345678910' })
      expect(response).toBe(companyMock)
    })

    it('should throw NotFoundException when company was not found', async () => {
      jest.spyOn(companyRepository, 'findOneBy').mockReturnValue(null)
      await expect(service.findOneBy({ cnpj: 'any' })).rejects.toThrowError(
        new NotFoundException(`Company was not found`)
      )
      expect(companyRepository.findOneBy).toBeCalledTimes(1)
      expect(companyRepository.findOneBy).toBeCalledWith({
        cnpj: 'any'
      })
    })
  })

  describe('create', () => {
    it('should use companyRepository .create and .save correctly', async () => {
      const company = await service.create(companyInput)
      expect(companyRepository.create).toBeCalledTimes(1)
      expect(companyRepository.save).toBeCalledTimes(1)
      expect(companyRepository.create).toBeCalledWith(companyInput)
      expect(companyRepository.save).toBeCalledWith(companyInput)
      expect(company).toBe(companyMock)
    })

    it('should throw InternalServerErrorException if .save or .create fails', async () => {
      jest.spyOn(companyRepository, 'create').mockImplementationOnce(() => {
        throw { any: 'any' }
      })
      await expect(service.create(companyInput)).rejects.toThrowError(new InternalServerErrorException({ any: 'any' }))
      expect(companyRepository.create).toBeCalledTimes(1)
      expect(companyRepository.create).toBeCalledWith(companyInput)
      expect(companyRepository.save).toBeCalledTimes(0)
    })
  })

  describe('update', () => {
    it('should return company correctly when updated', async () => {
      const response = await service.update('anyid', { cnpj: 'newcnpj' })
      expect(companyRepository.preload).toBeCalledTimes(1)
      expect(companyRepository.preload).toBeCalledWith({ idCompany: 'anyid', cnpj: 'newcnpj' })
      expect(companyRepository.save).toBeCalledTimes(1)
      expect(companyRepository.save).toBeCalledWith({
        idCompany: 'anyid',
        cnpj: 'newcnpj'
      })
      expect(response).toBe(companyMock)
    })

    it('should throw InternalServerErrorException when any method fails', async () => {
      jest.spyOn(companyRepository, 'preload').mockImplementationOnce(() => {
        throw { any: 'any' }
      })
      await expect(service.update('anyid', { cnpj: 'newcpnj' })).rejects.toThrowError(
        new InternalServerErrorException({ any: 'any' })
      )
      expect(companyRepository.preload).toBeCalledTimes(1)
      expect(companyRepository.preload).toBeCalledWith({ idCompany: 'anyid', cnpj: 'newcpnj' })
      expect(companyRepository.save).toBeCalledTimes(0)
    })
  })
})
