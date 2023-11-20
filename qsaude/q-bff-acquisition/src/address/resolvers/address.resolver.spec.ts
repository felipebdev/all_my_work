import { AddressService } from '@app/address/services/address.service'
import { Test, TestingModule } from '@nestjs/testing'
import { AddressResolver } from './address.resolver'
import { Address } from '@app/address/models/address.model'
import { PersonAddressInput } from '@app/address/models/address.input.model'
import { ProposalRoleEnum } from '@app/proposal/interfaces/enums/proposal.enum'

const addressMock: Address = {
  address: 'any',
  city: 'any',
  neighborhood: 'any',
  state: 'SP',
  zipCode: '00000000',
  addressComplement: 'any',
  addressNumber: '132'
}

const addressInput: PersonAddressInput = {
  zipCode: '13340501',
  address: 'Rua Y',
  addressNumber: '230',
  addressComplement: 'bairro X',
  neighborhood: 'bairro X',
  city: 'são paulo',
  state: 'SP',
  idProposal: 'anyid',
  role: ProposalRoleEnum.LegalRepresentative
}

describe('AddressResolver', () => {
  let resolver: AddressResolver
  let service: AddressService

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        AddressResolver,
        {
          provide: AddressService,
          useValue: {
            getAddressByPersonId: jest.fn(() => addressMock),
            createPersonAddress: jest.fn(() => addressMock)
          }
        }
      ]
    }).compile()

    resolver = module.get<AddressResolver>(AddressResolver)
    service = module.get<AddressService>(AddressService)
  })

  it('providers should be defined', () => {
    expect(resolver).toBeDefined()
    expect(service).toBeDefined()
  })

  describe('personAddress', () => {
    it('should return address', async () => {
      const address = await resolver.personAddress({ idProposal: 'anyid', role: ProposalRoleEnum.LegalRepresentative })
      expect(service.getAddressByPersonId).toBeCalledTimes(1)
      expect(service.getAddressByPersonId).toBeCalledWith({
        idProposal: 'anyid',
        role: ProposalRoleEnum.LegalRepresentative
      })
      expect(address).toStrictEqual(addressMock)
    })
  })
  describe('createPersonAddress', () => {
    it('should return created address', async () => {
      const address = await resolver.createPersonAddress(addressInput)
      expect(service.createPersonAddress).toBeCalledTimes(1)
      expect(service.createPersonAddress).toBeCalledWith(addressInput)
      expect(address).toStrictEqual(addressMock)
    })
  })
})
