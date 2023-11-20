import { ZipCodeEntity } from '@app/zip-code/entities'
import { AbstractZipCodeService } from '@app/zip-code/services'
import { Test, TestingModule } from '@nestjs/testing'
import { ZipCodeController } from './zip-code.controller'

const currDate = new Date()

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

describe('ZipCodeController', () => {
  let controller: ZipCodeController
  let service: AbstractZipCodeService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [ZipCodeController],
      providers: [
        {
          provide: AbstractZipCodeService,
          useValue: {
            findOne: jest.fn(() => zipCodeEntityMock)
          }
        }
      ]
    }).compile()

    controller = module.get<ZipCodeController>(ZipCodeController)
    service = module.get<AbstractZipCodeService>(AbstractZipCodeService)
  })

  it('should be defined', () => {
    expect(controller).toBeDefined()
  })

  it('service should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('getCep', () => {
    it('should return address from zipcodeservice', async () => {
      const address = await controller.getCep({ zipCode: '000000000' })
      expect(service.findOne).toBeCalledTimes(1)
      expect(service.findOne).toBeCalledWith('000000000')
      expect(address).toStrictEqual(zipCodeEntityMock)
    })
  })
})
