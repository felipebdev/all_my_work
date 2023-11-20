import { Test, TestingModule } from '@nestjs/testing'
import { AddressService } from './address.service'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ProposalRoleEnum } from '@app/proposal/interfaces/enums/proposal.enum'

const addressMock = {
  data: {
    zipCode: '13340501',
    address: 'Rua Y',
    addressNumber: '230',
    addressComplement: 'bairro X',
    neighborhood: 'bairro X',
    city: 'são paulo',
    state: 'SP',
    uuidAddress: '77ecd579-1e71-4202-b576-6c8a085fb968',
    createdAt: '2022-09-29T17:44:55.000Z',
    updatedAt: '2022-09-29T17:44:55.000Z'
  }
}

describe('AddressService', () => {
  let service: AddressService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'any-url')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        AddressService,
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              get: jest.fn(() => addressMock),
              post: jest.fn(() => addressMock)
            }
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<AddressService>(AddressService)
    httpService = module.get<HttpService>(HttpService)
  })

  it('services should be defined', () => {
    expect(service).toBeDefined()
    expect(httpService).toBeDefined()
  })

  describe('getAddressByPersonId', () => {
    it('should return person correctly when found', async () => {
      const response = await service.getAddressByPersonId({
        idProposal: 'any-id',
        role: ProposalRoleEnum.LegalResponsible
      })
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith('any-url/address/person/any-id?role=LEGAL_RESPONSIBLE')
      expect(response).toBe(addressMock.data)
    })

    it('should throw NotFoundException when address was not found', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 404, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(
        service.getAddressByPersonId({
          idProposal: 'any-id',
          role: ProposalRoleEnum.LegalResponsible
        })
      ).rejects.toThrowError(new NotFoundException(`ID XYZ WAS NOT FOUND`))
    })

    it('should throw InternalServerErrorException when status error is different than 404', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { response: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(
        service.getAddressByPersonId({
          idProposal: 'any-id',
          role: ProposalRoleEnum.LegalResponsible
        })
      ).rejects.toThrowError(new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' }))
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(
        service.getAddressByPersonId({
          idProposal: 'any-id',
          role: ProposalRoleEnum.LegalResponsible
        })
      ).rejects.toThrowError(new InternalServerErrorException({ message: 'Something went wrong.' }))
    })
  })

  describe('createPersonAddress', () => {
    const {
      data: { uuidAddress, ...address }
    } = addressMock

    const addressInput = {
      ...address,
      idProposal: 'anyid',
      role: ProposalRoleEnum.LegalRepresentative
    }

    it('should return created address when HTTP post works', async () => {
      const response = await service.createPersonAddress(addressInput)
      expect(httpService.axiosRef.post).toBeCalledTimes(1)
      expect(httpService.axiosRef.post).toBeCalledWith(
        'any-url/address/person/anyid?role=LEGAL_REPRESENTATIVE',
        address
      )
      expect(response).toBe(addressMock.data)
    })

    it('should throw InternalServerErrorException with response.data', async () => {
      jest.spyOn(httpService.axiosRef, 'post').mockImplementation(() => {
        throw { response: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.createPersonAddress(addressInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'ID XYZ WAS NOT FOUND' })
      )
    })

    it('should throw InternalServerErrorException with default message when unexpected error obj', async () => {
      jest.spyOn(httpService.axiosRef, 'post').mockImplementation(() => {
        throw { any: 'any' }
      })
      await expect(service.createPersonAddress(addressInput)).rejects.toThrowError(
        new InternalServerErrorException({ message: 'Something went wrong.' })
      )
    })
  })
})
