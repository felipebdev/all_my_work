import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { UnprocessableEntityException } from '@nestjs/common'
import { SerproService } from './serpro.service'

const serproQueueMock = {
  socios: [
    {
      cpf: '12345'
    }
  ],
  nomeEmpresarial: 'anyname12345'
}

const serproAuthMock = {
  access_token: '1234'
}

const mockConfigService = createMock<ConfigService>({
  get: jest.fn((val) => {
    switch (val) {
      case 'external-services.serpro.baseUrl':
        return 'serprourl'

      case 'external-services.serpro.accountKey':
        return 'serproaccountkey'

      case 'external-services.serpro.secretKey':
        return 'serprosecretkey'
      default:
        break
    }
  })
})

const httpServiceMock = {
  axiosRef: {
    get: jest.fn(() => ({ data: { ...serproQueueMock }, status: 200 })),
    post: jest.fn(() => ({ data: { ...serproAuthMock }, status: 200 }))
  }
}

describe('SerproService', () => {
  let service: SerproService

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule],
      providers: [
        SerproService,
        {
          provide: HttpService,
          useValue: httpServiceMock
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<SerproService>(SerproService)
  })

  it('controller and service should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('validateRelationsSerpro', () => {
    it('should throw UnprocessableEntityExcetion', async () => {
      jest
        .spyOn(httpServiceMock.axiosRef, 'post')
        .mockImplementationOnce(() => ({ data: { access_token: null }, status: 500 }))
      await expect(service.validateRelationsSerpro('qualquercnpj', '12345')).rejects.toThrow(
        new UnprocessableEntityException('Erro ao obter token Serpro')
      )
    })

    it('should return false', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'get').mockImplementationOnce(() => {
        throw {}
      })
      const response = await service.validateRelationsSerpro('qualquercnpj', '12345')
      expect(response).toBe(false)
    })

    it('should return true when a socio have same informed cpf', async () => {
      const response = await service.validateRelationsSerpro('qualquercnpj', '12345')
      expect(response).toBe(true)
    })

    it('should return false when none socio has informed cpf', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'get').mockImplementationOnce(() => ({
        data: {
          socios: [
            {
              cpf: 'anycpf'
            }
          ],
          nomeEmpresarial: 'any'
        },
        status: 200
      }))
      const response = await service.validateRelationsSerpro('qualquercnpj', '12345')
      expect(response).toBe(false)
    })

    it('should return true for MEI', async () => {
      jest
        .spyOn(httpServiceMock.axiosRef, 'get')
        .mockImplementationOnce(() => ({ data: { socios: undefined, nomeEmpresarial: '12345' }, status: 200 }))
      const response = await service.validateRelationsSerpro('qualquercnpj', '12345')
      expect(response).toBe(true)
    })

    it('should return true for individual company', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'get').mockImplementationOnce(() => ({
        data: { socios: undefined, nomeEmpresarial: 'Qualquer coisa Bonazzi' },
        status: 200
      }))
      const response = await service.validateRelationsSerpro('qualquercnpj', '12345', 'Felipe Bonazzi')
      expect(response).toBe(true)
    })

    it('should return false for individual company', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'get').mockImplementationOnce(() => ({
        data: { socios: undefined, nomeEmpresarial: 'Qualquer coisa NãoBonazzi' },
        status: 200
      }))
      const response = await service.validateRelationsSerpro('qualquercnpj', '12345', 'Felipe Bonazzi')
      expect(response).toBe(false)
    })

    it('should return true for companies as socios', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'get').mockImplementationOnce(() => ({
        data: { socios: [{ cpf: undefined, cnpj: '9999' }], nomeEmpresarial: 'Qualquer coisa Bonazzi' },
        status: 200
      }))
      jest.spyOn(httpServiceMock.axiosRef, 'get').mockImplementationOnce(() => ({
        data: { socios: [{ cpf: '12345' }], nomeEmpresarial: 'Qualquer coisa Bonazzi' },
        status: 200
      }))
      const response = await service.validateRelationsSerpro('qualquercnpj', '12345', 'Felipe Bonazzi')
      expect(response).toBe(true)
    })
  })
})
