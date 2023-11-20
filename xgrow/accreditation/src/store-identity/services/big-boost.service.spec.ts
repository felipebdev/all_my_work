import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { BigBoostService } from './big-boost.service'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { UnprocessableEntityException } from '@nestjs/common'

const mockConfigService = createMock<ConfigService>({
  get: jest.fn((val) => {
    switch (val) {
      case 'external-services.bigBoost.baseUrl':
        return 'bigBoosturl'

      case 'external-services.bigBoost.user':
        return 'user@user'

      case 'external-services.bigBoost.password':
        return 'password'

      default:
        break
    }
  })
})

const httpServiceMock = {
  axiosRef: {
    post: jest.fn((url) => {
      switch (url) {
        case 'bigBoosturl/tokens/generate':
          return { data: { token: 'anytoken', success: true } }

        case 'bigBoosturl/companies':
          return { data: { Result: [{ Relationships: { Relationships: [{ RelationshipType: 'ANY' }] } }] } }

        case 'bigBoosturl/peoplev2':
          return { data: { Result: [{ BasicData: { any: 'data' } }] } }

        default:
          break
      }
    })
  }
}

describe('BigBoostService', () => {
  let service: BigBoostService

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule],
      providers: [
        BigBoostService,
        {
          provide: HttpService,
          useValue: httpServiceMock
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<BigBoostService>(BigBoostService)
  })

  it('controller and service should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('authenticate', () => {
    it('should throw UnprocessableEntityException', async () => {
      jest
        .spyOn(httpServiceMock.axiosRef, 'post')
        .mockImplementationOnce(() => ({ data: { token: 'anytoken', success: false } }))
      await expect(service.cnpjRelationships('any')).rejects.toThrow(
        new UnprocessableEntityException('Erro ao obter token BigBoost')
      )
    })
  })

  describe('cnpjRelationships', () => {
    it('should return company ownership', async () => {
      const response = await service.cnpjRelationships('12345')
      expect(response).toStrictEqual([{ RelationshipType: 'ANY' }])
    })

    it('should return emptyArray', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'post').mockImplementation((url) => {
        switch (url) {
          case 'bigBoosturl/tokens/generate':
            return { data: { token: 'anytoken', success: true } }

          case 'bigBoosturl/companies':
            return { data: { Result: [{ Relationships: { Relationships: undefined } }] } }

          case 'bigBoosturl/peoplev2':
            return { data: { Result: [{ BasicData: { any: 'data' } }] } }

          default:
            break
        }
      })
      const response = await service.cnpjRelationships('12345')
      expect(response).toStrictEqual([])
    })
  })

  describe('personalData', () => {
    it('should return person BasicData', async () => {
      const response = await service.personalData('11111')
      expect(response).toStrictEqual({ any: 'data' })
    })

    it('should return undefined', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'post').mockImplementation((url) => {
        switch (url) {
          case 'bigBoosturl/tokens/generate':
            return { data: { token: 'anytoken', success: true } }

          case 'bigBoosturl/companies':
            return { data: { Result: [{ Relationships: { Relationships: [{ RelationshipType: 'any' }] } }] } }

          case 'bigBoosturl/peoplev2':
            return { data: undefined }

          default:
            break
        }
      })
      const response = await service.personalData('12345')
      expect(response).toBeUndefined()
    })
  })
})
