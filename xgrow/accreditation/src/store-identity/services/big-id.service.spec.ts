import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { BigIDService } from '@app/store-identity/services'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { BigIDValidatedDocument } from '@app/store-identity/interfaces'

const mockConfigService = createMock<ConfigService>({
  get: jest.fn((val) => {
    switch (val) {
      case 'external-services.bigId.baseUrl':
        return 'bigidurl'

      case 'external-services.bigId.accessToken':
        return 'bigidtoken'

      default:
        break
    }
  })
})

const httpServiceMock = { axiosRef: { post: jest.fn(() => ({ data: validatedDocumentMock })) } }

const validatedDocumentMock: BigIDValidatedDocument = {
  DocInfo: {
    CPF: '12345678...01',
    DOCTYPE: 'NEWRG',
    EXPEDITIONDATE: 'string',
    IDENTIFICATIONNUMBER: 'string',
    SIDE: 'string'
  },
  EstimatedInfo: {},
  TicketId: 'string',
  ResultCode: 2,
  ResultMessage: 'string'
}

const expectedHeaders = {
  Authorization: 'Bearer bigidtoken',
  'Content-Type': 'application/json'
}

describe('BigIdService', () => {
  let service: BigIDService

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule],
      providers: [
        BigIDService,
        {
          provide: HttpService,
          useValue: httpServiceMock
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<BigIDService>(BigIDService)
  })

  it('controller and service should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('ocr', () => {
    it('should return formattedcpf', async () => {
      const response = await service.ocr('base64file')
      expect(httpServiceMock.axiosRef.post).toBeCalledTimes(1)
      expect(httpServiceMock.axiosRef.post).toBeCalledWith(
        'bigidurl/VerifyID',
        {
          Parameters: ['DOC_IMG='.concat('base64file')]
        },
        { headers: expectedHeaders }
      )
      expect(response).toBe('1234567801')
    })

    it('should return null if DOCTYPE is undefined', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'post').mockImplementationOnce(() => ({
        data: {
          DocInfo: {
            DOCTYPE: undefined,
            CPF: '1234567801',
            EXPEDITIONDATE: 'string',
            IDENTIFICATIONNUMBER: 'string',
            SIDE: 'string'
          },
          EstimatedInfo: {},
          TicketId: 'string',
          ResultCode: 2,
          ResultMessage: 'string'
        }
      }))
      const response = await service.ocr('base64file')
      expect(httpServiceMock.axiosRef.post).toBeCalledTimes(1)
      expect(response).toBe(null)
    })

    it('should return null if CPF is undefined', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'post').mockImplementationOnce(() => ({
        data: {
          DocInfo: {
            DOCTYPE: 'any',
            CPF: undefined,
            EXPEDITIONDATE: 'string',
            IDENTIFICATIONNUMBER: 'string',
            SIDE: 'string'
          },
          EstimatedInfo: {},
          TicketId: 'string',
          ResultCode: 2,
          ResultMessage: 'string'
        }
      }))
      const response = await service.ocr('base64file')
      expect(httpServiceMock.axiosRef.post).toBeCalledTimes(1)
      expect(response).toBe(null)
    })

    // it('should return false if CPF is different than payload cpf', async () => {
    //   jest.spyOn(httpServiceMock.axiosRef, 'post').mockImplementationOnce(() => ({
    //     data: {
    //       DocInfo: {
    //         DOCTYPE: 'any',
    //         CPF: 'anything',
    //         EXPEDITIONDATE: 'string',
    //         IDENTIFICATIONNUMBER: 'string',
    //         SIDE: 'string'
    //       },
    //       EstimatedInfo: {},
    //       TicketId: 'string',
    //       ResultCode: 2,
    //       ResultMessage: 'string'
    //     }
    //   }))
    //   const response = await service.ocr('base64file')
    //   expect(httpServiceMock.axiosRef.post).toBeCalledTimes(1)
    //   expect(response).toBe(false)
    // })
  })
})
