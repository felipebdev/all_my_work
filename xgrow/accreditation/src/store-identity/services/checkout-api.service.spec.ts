import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { CheckoutAPIService } from '@app/store-identity/services'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { JwtService } from '@nestjs/jwt'

const mockConfigService = createMock<ConfigService>({
  get: jest.fn((val) => {
    switch (val) {
      case 'external-services.checkoutApi.baseUrl':
        return 'checkoutapiurl'
      default:
        break
    }
  })
})

const httpServiceMock = { axiosRef: { post: jest.fn(() => ({ data: 'anything' })) } }

const jwtServiceMock = {
  sign: jest.fn(() => 'jwttoken')
}

describe('CheckoutApiService', () => {
  let service: CheckoutAPIService

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule],
      providers: [
        CheckoutAPIService,
        {
          provide: HttpService,
          useValue: httpServiceMock
        },
        {
          provide: JwtService,
          useValue: jwtServiceMock
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<CheckoutAPIService>(CheckoutAPIService)
  })

  it('controller and service should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('createRecipient', () => {
    it('should return axios data', async () => {
      const response = await service.createRecipient('platformId', 'userId', 'correlationId')
      expect(httpServiceMock.axiosRef.post).toBeCalledTimes(1)
      expect(httpServiceMock.axiosRef.post).toBeCalledWith(
        'checkoutapiurl/recipients',
        {},
        {
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            Authorization: `Bearer jwttoken`,
            'X-Correlation-Id': 'correlationId'
          }
        }
      )
      expect(response).toBe('anything')
    })
  })
})
