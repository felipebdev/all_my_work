import { Test, TestingModule } from '@nestjs/testing'
import { ParametrizationService } from './Parametrization.service'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'

const mock = {
  idFileType: 14,
  saleTypeId: 1,
  beneficiaryType: "titular",
  mandatory: false,
  saleType: "pme",
  id: "695925cf-8a70-48ff-ac45-69ba51c42438",
  key: "document",
  name: "Anexos complementares 2"
}

const params = {
  beneficiaryType: "titular",
  saleType: "pme"
}

describe('ParametrizationService', () => {
  let service: ParametrizationService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'any-url')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        ParametrizationService,
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              get: jest.fn(() => mock),
              post: jest.fn(() => mock)
            }
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<ParametrizationService>(ParametrizationService)
    httpService = module.get<HttpService>(HttpService)
  })

  it('services should be defined', () => {
    expect(service).toBeDefined()
    expect(httpService).toBeDefined()
  })

  describe('parametrizationParametrization', () => {
    it('should return parametrization', async () => {
      const [response] = await service.getParametrization(params)
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith(`/parametrization/document?beneficiaryType=${params.beneficiaryType}&saleType=${params.saleType}`)
      expect(response.idFileType).toBe(mock.idFileType)
    })
  })
})
