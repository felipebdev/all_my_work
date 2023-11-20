import { Address } from '@app/address/models/address.model'
import { ZipCodeService } from '@app/zip-code/services/zip-code.service'
import { Test, TestingModule } from '@nestjs/testing'
import { ZipCodeResolver } from './zip-code.resolver'

const addressMock: Address = {
  address: 'any',
  city: 'any',
  neighborhood: 'any',
  state: 'SP',
  zipCode: '00000000'
}

describe('ZipCodeResolver', () => {
  let resolver: ZipCodeResolver
  let zipCodeService: ZipCodeService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        ZipCodeResolver,
        {
          provide: ZipCodeService,
          useValue: {
            getAddress: jest.fn(() => addressMock)
          }
        }
      ]
    }).compile()

    resolver = module.get<ZipCodeResolver>(ZipCodeResolver)
    zipCodeService = module.get<ZipCodeService>(ZipCodeService)
  })

  it('providers should be defined', () => {
    expect(resolver).toBeDefined()
    expect(zipCodeService).toBeDefined()
  })

  describe('zipCode', () => {
    it('should return address from zipCodeService', async () => {
      const address = await resolver.zipCode('00000000')
      expect(zipCodeService.getAddress).toBeCalledTimes(1)
      expect(zipCodeService.getAddress).toBeCalledWith('00000000')
      expect(address).toBe(addressMock)
    })
  })
})
