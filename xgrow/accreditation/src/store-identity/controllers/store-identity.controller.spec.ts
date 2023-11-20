import { ConfigModule } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { StoreIdentityController } from '@app/store-identity/controllers'
import { StoreIdentityService } from '@app/store-identity/services'
import { UserDocumentsDTO } from '@app/store-identity/dto'
import { createMock } from '@golevelup/nestjs-testing'

const serviceMock = {
  validateDocuments: jest.fn(() => true)
}

const fileMock = createMock<Express.Multer.File>()

const userDocumentsDTO: UserDocumentsDTO = {
  company_name: 'string',
  legal_name: 'string',
  first_name: 'string',
  last_name: 'string',
  document: 'string',
  bank_code: 'string',
  agency: 'string',
  agency_digit: 'string',
  account: 'string',
  account_digit: 'string',
  account_type: 'string',
  document_type: 'string'
}

describe('StoreIdentityController', () => {
  let controller: StoreIdentityController
  let service: StoreIdentityService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule],
      controllers: [StoreIdentityController],
      providers: [
        {
          provide: StoreIdentityService,
          useValue: serviceMock
        }
      ]
    }).compile()

    controller = module.get<StoreIdentityController>(StoreIdentityController)
    service = module.get<StoreIdentityService>(StoreIdentityService)
  })

  it('controller and service should be defined', () => {
    expect(controller).toBeDefined()
    expect(service).toBeDefined()
  })

  it('should return validation', async () => {
    const response = await controller.validateDocuments(
      fileMock,
      userDocumentsDTO,
      { 'x-correlation-id': '1234' },
      { user: 'anyUser' }
    )
    expect(serviceMock.validateDocuments).toBeCalledTimes(1)
    expect(serviceMock.validateDocuments).toBeCalledWith(fileMock, userDocumentsDTO, '1234', 'anyUser')
    expect(response).toBe(true)
  })
})
