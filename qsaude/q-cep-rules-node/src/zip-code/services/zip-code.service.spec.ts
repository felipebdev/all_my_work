import { Test, TestingModule } from '@nestjs/testing'
import { ZipCodeService } from './zip-code.service'
import { Repository } from 'typeorm'
import { ZipCodeEntity } from '@app/zip-code/entities'
import { HttpService } from '@nestjs/axios'
import { getRepositoryToken } from '@nestjs/typeorm'
import { createMock } from '@golevelup/nestjs-testing'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { NotFoundException } from '@nestjs/common'

const currDate = new Date()

const viaCepResponseMock = {
  cep: '13340-501',
  logradouro: 'Rua Alemanha',
  complemento: '',
  bairro: 'Chácara do Trevo',
  localidade: 'Indaiatuba',
  uf: 'SP',
  ibge: '3520509',
  gia: '3530',
  ddd: '19',
  siafi: '6511'
}

const zipCodeEntityMock: ZipCodeEntity = {
  address: 'any',
  city: 'any',
  district: 'any',
  idZipCode: 'any',
  createdAt: currDate,
  updatedAt: currDate,
  ibgeCode: 'any',
  state: 'SP',
  zipCode: '00000000'
}

describe('ZipCodeService', () => {
  let service: ZipCodeService
  let zipCodeRepo: Repository<ZipCodeEntity>
  let httpService: HttpService

  const ZIP_CODE_REPOSITORY_TOKEN = getRepositoryToken(ZipCodeEntity)

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'viacepurl')
  })

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        ZipCodeService,
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              get: jest.fn(() => ({ data: viaCepResponseMock }))
            }
          }
        },
        {
          provide: ZIP_CODE_REPOSITORY_TOKEN,
          useValue: {
            create: jest.fn((args) => args),
            save: jest.fn((args) => ({ ...args, idZipCode: 'anyid' })),
            findOneBy: jest.fn(() => zipCodeEntityMock)
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<ZipCodeService>(ZipCodeService)
    zipCodeRepo = module.get<Repository<ZipCodeEntity>>(ZIP_CODE_REPOSITORY_TOKEN)
    httpService = module.get<HttpService>(HttpService)
  })

  it('services should be defined', () => {
    expect(service).toBeDefined()
    expect(httpService).toBeDefined()
  })

  it('repository should be defined', () => {
    expect(zipCodeRepo).toBeDefined()
  })

  describe('findOne', () => {
    it('should return address from repo if found', async () => {
      const address = await service.findOne('000000000')
      expect(zipCodeRepo.findOneBy).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledTimes(0)
      expect(zipCodeRepo.findOneBy).toBeCalledWith({
        zipCode: '000000000'
      })
      expect(address).toStrictEqual(zipCodeEntityMock)
    })
    it('should return created address from viacep if not found in database', async () => {
      jest.spyOn(zipCodeRepo, 'findOneBy').mockReturnValueOnce(null)
      const address = await service.findOne('000000000')
      expect(zipCodeRepo.findOneBy).toBeCalledTimes(1)
      expect(zipCodeRepo.findOneBy).toBeCalledWith({
        zipCode: '000000000'
      })
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith('viacepurl/000000000/json')
      expect(zipCodeRepo.create).toBeCalledTimes(1)
      expect(zipCodeRepo.create).toBeCalledWith({
        zipCode: '13340501',
        address: 'Rua Alemanha',
        district: 'Chácara do Trevo',
        city: 'Indaiatuba',
        state: 'SP',
        ibgeCode: '3520509'
      })
      expect(zipCodeRepo.save).toBeCalledTimes(1)
      expect(zipCodeRepo.save).toBeCalledWith({
        zipCode: '13340501',
        address: 'Rua Alemanha',
        district: 'Chácara do Trevo',
        city: 'Indaiatuba',
        state: 'SP',
        ibgeCode: '3520509'
      })
      expect(address).toStrictEqual({
        zipCode: '13340501',
        address: 'Rua Alemanha',
        district: 'Chácara do Trevo',
        city: 'Indaiatuba',
        state: 'SP',
        ibgeCode: '3520509',
        idZipCode: 'anyid'
      })
    })
    it('should throw NotFoundException if cep from viacep is null', async () => {
      const { cep, ...viaCepResponse } = viaCepResponseMock
      jest.spyOn(zipCodeRepo, 'findOneBy').mockReturnValueOnce(null)
      jest.spyOn(httpService.axiosRef, 'get').mockReturnValueOnce({ data: { ...viaCepResponse, cep: '' } } as any)
      await expect(service.findOne('00000000')).rejects.toThrow(new NotFoundException('Zip code 00000000 not found'))
    })
    it('should throw NotFoundException if property erro from viacep is exists', async () => {
      jest.spyOn(zipCodeRepo, 'findOneBy').mockReturnValueOnce(null)
      jest
        .spyOn(httpService.axiosRef, 'get')
        .mockReturnValueOnce({ data: { viaCepResponseMock, erro: 'anyerror' } } as any)
      await expect(service.findOne('00000000')).rejects.toThrow(new NotFoundException('Zip code 00000000 not found'))
    })
  })
})
