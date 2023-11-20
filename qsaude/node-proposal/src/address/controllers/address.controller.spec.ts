import { Test, TestingModule } from '@nestjs/testing'
import { CreatePersonAddressDto, CreateAddressDto, CreateCompanyAddressDto } from '@app/address/dtos'

import { AddressController } from './address.controller'
import { AbstractAddressService } from '../services/abstract/address.service.abstract'
import { ProposalRole } from '@app/proposal/interfaces'

const mock: CreateAddressDto = {
  address: 'Street Name',
  addressComplement: 'Complement to street',
  addressNumber: '100',
  city: 'City',
  neighborhood: 'Neighborhood name',
  state: 'State name',
  zipCode: '98005100'
}

describe('AddressController', () => {
  let controller: AddressController
  let AddressService: AbstractAddressService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
          provide: AbstractAddressService,
          useValue: {
            createPersonAddress: jest.fn(() => mock),
            createCompanyAddress: jest.fn(() => mock),
            findOne: jest.fn(() => mock)
          }
        }
      ],
      controllers: [AddressController]
    }).compile()
    controller = module.get<AddressController>(AddressController)
    AddressService = module.get<AbstractAddressService>(AbstractAddressService)
  })

  it('controller should be defined', () => {
    expect(controller).toBeDefined()
  })

  it('service should be defined', () => {
    expect(AddressService).toBeDefined()
  })

  describe('createAddress', () => {
    it('should return created Address when AddressService works correctly', async () => {
      const response = await controller.createPersonAddress('anyIdProposal', ProposalRole.LEGAL_REPRESENTATIVE, mock)
      expect(AddressService.createPersonAddress).toBeCalledTimes(1)
      expect(AddressService.createPersonAddress).toBeCalledWith(
        'anyIdProposal',
        ProposalRole.LEGAL_REPRESENTATIVE,
        mock
      )
      expect(response).toStrictEqual(mock)
    })
  })

  describe('findOne', () => {
    it('should return Address when AddressService works correctly', async () => {
      const response = await controller.findOne('any')
      expect(AddressService.findOne).toBeCalledTimes(1)
      expect(AddressService.findOne).toBeCalledWith('any')
      expect(response).toStrictEqual(mock)
    })
  })

  describe('createCompanyAddress', () => {
    const createCompanyAddressInput: CreateCompanyAddressDto = {
      ...mock,
      idCompany: 'anyid'
    }

    it('should return created Company Address when AddressService works correctly', async () => {
      const response = await controller.createCompanyAddress(createCompanyAddressInput)
      expect(AddressService.createCompanyAddress).toBeCalledTimes(1)
      expect(AddressService.createCompanyAddress).toBeCalledWith(createCompanyAddressInput)
      expect(response).toStrictEqual(mock)
    })
  })
})
