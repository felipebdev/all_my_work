import { Test, TestingModule } from '@nestjs/testing'
import { createMock } from '@golevelup/nestjs-testing'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { HttpService } from '@nestjs/axios'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { PersonService } from '@app/person/services/person.service'

const personMock = {
  data: {
    person: {
      idPerson: '3a3ab697-8d71-4d95-9bab-95f51fbfd631',
      name: 'Felipe Bonazzi Hahaha',
      socialName: 'Felipe Edited',
      birthday: '1998-12-24T02:00:00.000Z',
      gender: '1',
      maritalStatus: 'S',
      cpf: '50783426801',
      cns: '279975703360004',
      rg: '550956542',
      emittingOrgan: 'SSP Edited'
    }
  }
}

describe('PersonService', () => {
  let service: PersonService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'any-url')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        PersonService,
        {
          provide: HttpService,
          useValue: { axiosRef: { get: jest.fn(() => personMock) } }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<PersonService>(PersonService)
    httpService = module.get<HttpService>(HttpService)
  })

  it('should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('getPersonById', () => {
    it('should return person correctly when found', async () => {
      const response = await service.getPersonById('any-id')
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith('any-url/person/any-id')
      expect(response).toBe(personMock.data)
    })

    it('should throw NotFoundException when Person was not found', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 404, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.getPersonById('any')).rejects.toThrowError(new NotFoundException(`ID XYZ WAS NOT FOUND`))
    })

    it('should throw InternalServerErrorException when status error is different than 404', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 200, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.getPersonById('any')).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.getPersonById('any')).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })
})
