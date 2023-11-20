import { Test, TestingModule } from '@nestjs/testing'
import { BigIDService, StoreIdentityService, NextcodeService } from '@app/store-identity/services'
import { createMock } from '@golevelup/nestjs-testing'
import { GCPStorageService } from '@app/common/services'
import { UserDocumentsDTO } from '@app/store-identity/dto'
import { PassThrough } from 'stream'
import { BigBoostService } from './big-boost.service'
import { SerproService } from './serpro.service'
import { UnprocessableEntityException, NotFoundException } from '@nestjs/common'
import { CheckoutAPIService } from './checkout-api.service'

const mockBigIdService = {
  ocr: jest.fn(() => '99999999')
}

const mockNextCodeService = {
  ocr: jest.fn(() => '99999999')
}

const mockBigBoostService = {
  cnpjRelationships: jest.fn(() => [{ RelatedEntityTaxIdNumber: '99999999' }]),
  personalData: jest.fn(() => ({ Name: 'Felipe Bonazzi' }))
}

const mockSerproService = {
  validateRelationsSerpro: jest.fn(() => true)
}

const mockGCPService = createMock<GCPStorageService>({
  uploadFile: jest.fn()
})

const mockCheckoutService = createMock<CheckoutAPIService>({
  createRecipient: jest.fn()
})

const fileMock: Express.Multer.File = {
  originalname: 'file.csv',
  mimetype: 'text/csv',
  path: 'something',
  buffer: Buffer.from('anyvalue'),
  fieldname: '',
  destination: '',
  filename: '',
  size: 1,
  stream: new PassThrough(),
  encoding: ''
}

const userDocumentsDTO: UserDocumentsDTO = {
  company_name: 'string',
  legal_name: 'string',
  first_name: 'string',
  last_name: 'string',
  document: '99999999',
  bank_code: 'string',
  agency: 'string',
  agency_digit: 'string',
  account: 'string',
  account_digit: 'string',
  account_type: 'string',
  document_type: 'cpf'
}

describe('StoreIdentityService', () => {
  let service: StoreIdentityService

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        StoreIdentityService,
        {
          provide: BigIDService,
          useValue: mockBigIdService
        },
        {
          provide: NextcodeService,
          useValue: mockNextCodeService
        },
        {
          provide: GCPStorageService,
          useValue: mockGCPService
        },
        {
          provide: BigBoostService,
          useValue: mockBigBoostService
        },
        {
          provide: SerproService,
          useValue: mockSerproService
        },
        {
          provide: CheckoutAPIService,
          useValue: mockCheckoutService
        }
      ]
    }).compile()

    service = module.get<StoreIdentityService>(StoreIdentityService)
  })

  it('should be defined', () => {
    expect(service).toBeDefined()
  })

  it('should return bigId ocr validation', async () => {
    const response = await service.validateDocuments(fileMock, userDocumentsDTO, 'correlationId', {
      platform_id: 'platform_id',
      user_id: 'platform_id'
    })
    expect(mockGCPService.uploadFile).toBeCalledTimes(1)
    expect(mockGCPService.uploadFile).toBeCalledWith(fileMock, 'documents')
    expect(mockBigIdService.ocr).toBeCalledTimes(1)
    expect(mockBigIdService.ocr).toBeCalledWith(fileMock.buffer.toString('base64'))
    expect(response).toBe(true)
  })

  it('should return nextcode ocr validation', async () => {
    jest.spyOn(mockBigIdService, 'ocr').mockImplementationOnce(() => null)
    const response = await service.validateDocuments(fileMock, userDocumentsDTO, 'correlationId', {
      platform_id: 'platform_id',
      user_id: 'platform_id'
    })
    expect(mockGCPService.uploadFile).toBeCalledTimes(1)
    expect(mockGCPService.uploadFile).toBeCalledWith(fileMock, 'documents')
    expect(mockBigIdService.ocr).toBeCalledTimes(1)
    expect(mockNextCodeService.ocr).toBeCalledTimes(1)
    expect(mockNextCodeService.ocr).toBeCalledWith(fileMock.filename, fileMock.buffer.toString('base64'))
    expect(response).toBe(true)
  })

  it('should throw UnprocessableEntityException', async () => {
    jest.spyOn(mockBigIdService, 'ocr').mockImplementationOnce(() => '123456')
    await expect(
      service.validateDocuments(fileMock, userDocumentsDTO, 'correlationId', {
        platform_id: 'platform_id',
        user_id: 'platform_id'
      })
    ).rejects.toThrow(
      new UnprocessableEntityException({
        validate_error: true,
        message: 'O Documento informado não pertence ao usuário'
      })
    )
    expect(mockGCPService.uploadFile).toBeCalledTimes(1)
    expect(mockGCPService.uploadFile).toBeCalledWith(fileMock, 'documents')
    expect(mockBigIdService.ocr).toBeCalledTimes(1)
  })

  it('should return true for bigBoostValidation', async () => {
    const payload = {
      ...userDocumentsDTO,
      document_type: 'cnpj'
    }
    const response = await service.validateDocuments(fileMock, payload, 'correlationId', {
      platform_id: 'platform_id',
      user_id: 'platform_id'
    })
    expect(response).toBe(true)
  })

  it('should return true for bigBoostValidation with cnpjs as RelatedEntityTaxIdNumber', async () => {
    const payload = {
      ...userDocumentsDTO,
      document_type: 'cnpj'
    }
    jest
      .spyOn(mockBigBoostService, 'cnpjRelationships')
      .mockImplementationOnce(() => [{ RelatedEntityTaxIdNumber: '1123123123123123123213' }])
    jest
      .spyOn(mockBigBoostService, 'cnpjRelationships')
      .mockImplementationOnce(() => [{ RelatedEntityTaxIdNumber: '99999999' }])
    const response = await service.validateDocuments(fileMock, payload, 'correlationId', {
      platform_id: 'platform_id',
      user_id: 'platform_id'
    })
    expect(response).toBe(true)
  })

  it('should return true for serProValidation', async () => {
    const payload = {
      ...userDocumentsDTO,
      document_type: 'cnpj'
    }
    jest
      .spyOn(mockBigBoostService, 'cnpjRelationships')
      .mockImplementationOnce(() => [{ RelatedEntityTaxIdNumber: '1123123123123123123213' }])
    jest
      .spyOn(mockBigBoostService, 'cnpjRelationships')
      .mockImplementationOnce(() => [{ RelatedEntityTaxIdNumber: 'anything' }])
    const response = await service.validateDocuments(fileMock, payload, 'correlationId', {
      platform_id: 'platform_id',
      user_id: 'platform_id'
    })
    expect(response).toBe(true)
  })

  it('should throw NotFoundException', async () => {
    const payload = {
      ...userDocumentsDTO,
      document_type: 'cnpj'
    }
    jest
      .spyOn(mockBigBoostService, 'cnpjRelationships')
      .mockImplementationOnce(() => [{ RelatedEntityTaxIdNumber: '1123123123123123123213' }])
    jest
      .spyOn(mockBigBoostService, 'cnpjRelationships')
      .mockImplementationOnce(() => [{ RelatedEntityTaxIdNumber: 'anything' }])

    jest.spyOn(mockSerproService, 'validateRelationsSerpro').mockImplementationOnce(() => false)
    await expect(
      service.validateDocuments(fileMock, payload, 'correlationId', {
        platform_id: 'platform_id',
        user_id: 'platform_id'
      })
    ).rejects.toThrow(new NotFoundException('O CNPJ 99999999 não pertence ao CPF 99999999'))
  })
})
