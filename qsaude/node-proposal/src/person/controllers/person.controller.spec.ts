import { Test, TestingModule } from '@nestjs/testing'
import { Gender } from '@app/person/interfaces/enums'
import { PersonController } from '@app/person/controllers/person.controller'
import { AbstractPersonService } from '@app/person/services'

const personMock = {
  idPerson: '3a3ab697-8d71-4d95-9bab-95f51fbfd631',
  name: 'Felipe Bonazzi Hahaha',
  socialName: 'Felipe Edited',
  birthday: '1998-12-24',
  gender: Gender.Male,
  maritalStatus: 'S',
  cpf: '50783426801',
  cns: '279975703360004',
  rg: '550956542',
  emittingOrgan: 'SSP Edited',
  motherName: 'Sandra Regina Edited',
  updatedAt: '2022-09-18T20:01:53.000Z'
}
describe('PersonController', () => {
  let controller: PersonController
  let personService: AbstractPersonService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
          provide: AbstractPersonService,
          useValue: {
            createOrUpdate: jest.fn(() => personMock),
            findOne: jest.fn(() => personMock)
          }
        }
      ],
      controllers: [PersonController]
    }).compile()
    controller = module.get<PersonController>(PersonController)
    personService = module.get<AbstractPersonService>(AbstractPersonService)
  })

  it('controller should be defined', () => {
    expect(controller).toBeDefined()
  })

  it('service should be defined', () => {
    expect(personService).toBeDefined()
  })

  describe('createPerson', () => {
    it('should return created person when PersonService works correctly', async () => {
      const response = await controller.createPerson(personMock)
      expect(personService.createOrUpdate).toBeCalledTimes(1)
      expect(personService.createOrUpdate).toBeCalledWith(personMock)
      expect(response).toBe(personMock)
    })

    it('throw an error when personService fails', async () => {
      jest.spyOn(personService, 'createOrUpdate').mockImplementation(() => {
        throw new Error('x')
      })
      await expect(controller.createPerson(personMock)).rejects.toThrowError('x')
      expect(personService.createOrUpdate).toBeCalledTimes(1)
      expect(personService.createOrUpdate).toBeCalledWith(personMock)
    })
  })

  describe('findOne', () => {
    it('should return person when PersonService works correctly', async () => {
      const response = await controller.findOne('any')
      expect(personService.findOne).toBeCalledTimes(1)
      expect(personService.findOne).toBeCalledWith('any')
      expect(response).toBe(personMock)
    })

    it('throw an error when PersonService fails', async () => {
      jest.spyOn(personService, 'findOne').mockImplementation(() => {
        throw new Error('x')
      })
      await expect(controller.findOne('any')).rejects.toThrowError('x')
      expect(personService.findOne).toBeCalledTimes(1)
      expect(personService.findOne).toBeCalledWith('any')
    })
  })
})
