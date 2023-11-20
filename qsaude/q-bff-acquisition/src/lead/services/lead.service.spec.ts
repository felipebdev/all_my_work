import { createMock } from '@golevelup/nestjs-testing'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { LeadService } from './lead.service'
import { HttpService } from '@nestjs/axios'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'

const leadMock = {
  data: {
    uuidLead: 'any',
    numberCPF: 'any',
    completeName: 'José da Silva',
    birthday: '2022-06-10T03:00:00.000Z',
    codePlan: '0030',
    email: 'email@email.com',
    CellPhoneDDD: '11',
    cellPhoneNumber: '987456321',
    tagName: null
  }
}

describe('LeadResolver', () => {
  let service: LeadService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'any-url')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        LeadService,
        {
          provide: HttpService,
          useValue: { axiosRef: { get: jest.fn(() => leadMock) } }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<LeadService>(LeadService)
    httpService = module.get<HttpService>(HttpService)
  })

  it('service should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('findOne', () => {
    it('should return lead correctly when found', async () => {
      const response = await service.getLeadById('any-id')
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith('any-url/lead/any-id')
      expect(response).toBe(leadMock.data)
    })

    it('should throw NotFoundException when Lead was not found', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 404, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.getLeadById('any')).rejects.toThrowError(new NotFoundException(`ID XYZ WAS NOT FOUND`))
    })

    it('should throw InternalServerErrorException when status error is different than 404', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 200, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.getLeadById('any')).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.getLeadById('any')).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })
})
