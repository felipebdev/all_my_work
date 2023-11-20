import { CompanyEntity } from '@app/company/entities'
import { CompanySize } from '@app/company/interfaces'
import { Test, TestingModule } from '@nestjs/testing'
import { CompanyController } from './company.controller'
import { AbstractCompanyService } from '../services/abstract/company.service.abstract'
import { CreateCompanyUuidDto } from '@app/company/dtos'

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

describe('CompanyController', () => {
  let controller: CompanyController
  let companyService: AbstractCompanyService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
          provide: AbstractCompanyService,
          useValue: {
            findOneBy: jest.fn(() => companyMock),
            create: jest.fn(() => companyMock),
            update: jest.fn(() => companyMock)
          }
        }
      ],
      controllers: [CompanyController]
    }).compile()

    controller = module.get<CompanyController>(CompanyController)
    companyService = module.get<AbstractCompanyService>(AbstractCompanyService)
  })

  it('controller should be defined', () => {
    expect(controller).toBeDefined()
  })

  it('service should be defined', () => {
    expect(companyService).toBeDefined()
  })

  describe('get', () => {
    it('should return found company from service', async () => {
      const response = await controller.getCompanyByProposal({ idProposal: 'anyid' })
      expect(companyService.findOneBy).toBeCalledTimes(1)
      expect(companyService.findOneBy).toBeCalledWith({ idProposal: 'anyid' })
      expect(response).toBe(companyMock)
    })
  })
  describe('create', () => {
    it('should return created company from service', async () => {
      const response = await controller.create(companyInput)
      expect(companyService.create).toBeCalledTimes(1)
      expect(companyService.create).toBeCalledWith(companyInput)
      expect(response).toBe(companyMock)
    })
  })
  describe('update', () => {
    it('should return updated company from service', async () => {
      const response = await controller.update({ idCompany: 'anyid' }, companyInput)
      expect(companyService.update).toBeCalledTimes(1)
      expect(companyService.update).toBeCalledWith('anyid', companyInput)
      expect(response).toBe(companyMock)
    })
  })
})
