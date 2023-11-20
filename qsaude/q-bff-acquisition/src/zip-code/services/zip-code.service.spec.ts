import { createMock } from '@golevelup/nestjs-testing'
import { ConfigService, ConfigModule } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { ZipCodeService } from './zip-code.service'
import { HttpService } from '@nestjs/axios'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'

const zipCodeResponseMock = {
  zipCode: '00000000',
  address: 'Rua Any',
  district: 'Bairro any',
  state: 'SP',
  city: 'Any City'
}

describe('ZipCodeService', () => {
  let service: ZipCodeService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'zip-code-url')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        ZipCodeService,
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              get: jest.fn(() => ({ data: zipCodeResponseMock }))
            }
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<ZipCodeService>(ZipCodeService)
    httpService = module.get<HttpService>(HttpService)
  })

  it('services should be defined', () => {
    expect(service).toBeDefined()
    expect(httpService).toBeDefined()
  })

  describe('getAddress', () => {
    it('should return address correctly when found', async () => {
      const response = await service.getAddress('000000000')
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith('zip-code-url/cep/000000000')
      expect(response).toStrictEqual({
        address: zipCodeResponseMock.address,
        city: zipCodeResponseMock.city,
        neighborhood: zipCodeResponseMock.district,
        state: zipCodeResponseMock.state,
        zipCode: zipCodeResponseMock.zipCode
      })
    })

    it('should throw NotFoundException when Lead was not found', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 404, data: { message: 'ZIPCODE XYZ WAS NOT FOUND' } } }
      })
      await expect(service.getAddress('000000000')).rejects.toThrowError(
        new NotFoundException(`ZIPCODE XYZ WAS NOT FOUND`)
      )
    })

    it('should throw InternalServerErrorException when status error is different than 404', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 200, data: { message: 'ZIPCODE XYZ WAS NOT FOUND' } } }
      })
      await expect(service.getAddress('000000000')).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ZIPCODE XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.getAddress('000000000')).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })
})
