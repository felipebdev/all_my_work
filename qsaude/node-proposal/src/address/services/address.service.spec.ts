import { AddressEntity } from '@app/address/entities'
import { AddressService } from '@app/address/services/address.service'
import { Test, TestingModule } from '@nestjs/testing'
import { getRepositoryToken } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { CreatePersonAddressDto, CreateAddressDto, CreateCompanyAddressDto } from '@app/address/dtos'
import { AbstractContactService } from '@app/contact/services/abstract/contact.service.abstract'
import { ContactEntity } from '../../contact/entities/contact.entity'
import { ContactRefType, ContactType } from '@app/contact/interfaces/contact.enum'
import { ProposalRole } from '@app/proposal/interfaces'
import { AbstractProposalRoleService } from '../../proposal/services/abstract/proposal-role.service.abstract'

const mock: CreateAddressDto = {
  address: 'Street Name',
  addressComplement: 'Complement to street',
  addressNumber: '100',
  city: 'City',
  neighborhood: 'Neighborhood name',
  state: 'State name',
  zipCode: '98005100'
}

const contactMock: ContactEntity = {
  refId: 'anyid',
  refType: ContactRefType.proposalRole,
  type: ContactType.address,
  value: 'anyvalue',
  idContact: 'anyid'
}

describe('AddressService', () => {
  let service: AddressService
  let addressRepository: Repository<AddressEntity>
  let contactService: AbstractContactService

  const ADDRESS_REPOSITORY_TOKEN = getRepositoryToken(AddressEntity)

  beforeEach(async () => {
    jest.clearAllTimers()
    jest.clearAllMocks()
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        AddressService,
        {
          provide: ADDRESS_REPOSITORY_TOKEN,
          useValue: {
            create: jest.fn((args) => args),
            save: jest.fn((args) => args),
            findOne: jest.fn(() => mock)
          }
        },
        {
          provide: AbstractContactService,
          useValue: {
            create: jest.fn((args) => args),
            findOneBy: jest.fn(() => contactMock)
          }
        },
        {
          provide: AbstractProposalRoleService,
          useValue: {
            findOneBy: jest.fn(() => ({ idProposalRole: 'anyid' }))
          }
        }
      ]
    }).compile()

    service = module.get<AddressService>(AddressService)
    contactService = module.get<AbstractContactService>(AbstractContactService)
    addressRepository = module.get<Repository<AddressEntity>>(ADDRESS_REPOSITORY_TOKEN)
  })

  it('services should be defined', () => {
    expect(service).toBeDefined()
    expect(contactService).toBeDefined()
  })

  it('addressRepository should be defined', () => {
    expect(addressRepository).toBeDefined()
  })

  describe('createPersonAddress', () => {
    it('should use addressRepository.save method correctly', async () => {
      const person = await service.createPersonAddress('anyIdProposal', ProposalRole.LEGAL_RESPONSIBLE, mock)
      expect(addressRepository.create).toBeCalledTimes(1)
      expect(addressRepository.save).toBeCalledTimes(1)
      expect(addressRepository.create).toBeCalledWith(mock)
      expect(addressRepository.save).toBeCalledWith(mock)
      expect(person).toStrictEqual(mock)
    })

    it('should throw InternalServerErrorException if .save or .create fails', async () => {
      jest.spyOn(addressRepository, 'create').mockImplementationOnce(() => {
        throw { any: 'any' }
      })
      await expect(
        service.createPersonAddress('anyIdProposal', ProposalRole.LEGAL_RESPONSIBLE, mock)
      ).rejects.toThrowError(new InternalServerErrorException({ any: 'any' }))
      expect(addressRepository.create).toBeCalledTimes(1)
      expect(addressRepository.create).toBeCalledWith(mock)
      expect(addressRepository.save).toBeCalledTimes(0)
    })
  })

  describe('findOne', () => {
    it('should return address correctly when found', async () => {
      const response = await service.findOne('any')
      expect(addressRepository.findOne).toBeCalledTimes(1)
      expect(addressRepository.findOne).toBeCalledWith({
        where: {
          uuidAddress: 'any'
        }
      })
      expect(response).toStrictEqual(mock)
    })

    it('should throw NotFoundException when Person was not found', async () => {
      jest.spyOn(addressRepository, 'findOne').mockReturnValue(null)
      await expect(service.findOne('any')).rejects.toThrowError(new NotFoundException(`Address #any was not found`))
      expect(addressRepository.findOne).toBeCalledTimes(1)
      expect(addressRepository.findOne).toBeCalledWith({
        where: {
          uuidAddress: 'any'
        }
      })
    })
  })

  describe('findOneByPerson', () => {
    it('should return address correctly when found', async () => {
      const serviceFindOneSpy = jest.spyOn(service, 'findOne')
      const response = await service.findOneByProposalRole('anyIdProposal', ProposalRole.LEGAL_REPRESENTATIVE)
      expect(contactService.findOneBy).toBeCalledTimes(1)
      expect(contactService.findOneBy).toBeCalledWith({
        refId: 'anyid',
        refType: ContactRefType.proposalRole,
        type: ContactType.address
      })
      expect(serviceFindOneSpy).toBeCalledTimes(1)
      expect(response).toStrictEqual(mock)
    })
  })

  describe('createCompanyAddress', () => {
    const createCompanyAddressInput: CreateCompanyAddressDto = {
      ...mock,
      idCompany: 'anyid'
    }

    it('should use addressRepository.save method correctly', async () => {
      const person = await service.createCompanyAddress(createCompanyAddressInput)
      expect(addressRepository.create).toBeCalledTimes(1)
      expect(addressRepository.save).toBeCalledTimes(1)
      expect(addressRepository.create).toBeCalledWith(mock)
      expect(addressRepository.save).toBeCalledWith(mock)
      expect(person).toStrictEqual(mock)
    })

    it('should throw InternalServerErrorException if .save or .create fails', async () => {
      jest.spyOn(addressRepository, 'create').mockImplementationOnce(() => {
        throw { any: 'any' }
      })
      await expect(service.createCompanyAddress(createCompanyAddressInput)).rejects.toThrowError(
        new InternalServerErrorException({ any: 'any' })
      )
      expect(addressRepository.create).toBeCalledTimes(1)
      expect(addressRepository.create).toBeCalledWith(mock)
      expect(addressRepository.save).toBeCalledTimes(0)
    })
  })

  describe('findOneByCompany', () => {
    it('should return address correctly when found', async () => {
      const serviceFindOneSpy = jest.spyOn(service, 'findOne')
      const response = await service.findOneByCompany('anycompanyid')
      expect(contactService.findOneBy).toBeCalledTimes(1)
      expect(contactService.findOneBy).toBeCalledWith({
        refId: 'anycompanyid',
        refType: ContactRefType.company,
        type: ContactType.address
      })
      expect(serviceFindOneSpy).toBeCalledTimes(1)
      expect(response).toStrictEqual(mock)
    })
  })
})
