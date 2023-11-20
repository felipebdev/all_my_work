import { PersonService } from '@app/person/services/person.service'
import { Test, TestingModule } from '@nestjs/testing'
import { PersonResolver } from './person.resolver'

const personMock = {
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

describe('PersonResolver', () => {
  let resolver: PersonResolver
  let personService: PersonService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        PersonResolver,
        {
          provide: PersonService,
          useValue: { getPersonById: jest.fn(() => personMock) }
        }
      ]
    }).compile()

    resolver = module.get<PersonResolver>(PersonResolver)
    personService = module.get<PersonService>(PersonService)
  })

  it('should be defined', () => {
    expect(resolver).toBeDefined()
  })

  it('personService should be defined', () => {
    expect(personService).toBeDefined()
  })

  describe('person', () => {
    it('should return person correctly when found', async () => {
      const response = await resolver.person('any')
      expect(personService.getPersonById).toBeCalledTimes(1)
      expect(personService.getPersonById).toBeCalledWith('any')
      expect(response).toBe(personMock)
    })

    it('should throw custom exception when PersonService fails', async () => {
      jest.spyOn(personService, 'getPersonById').mockImplementation(() => {
        throw new Error('x')
      })
      await expect(resolver.person('any')).rejects.toThrowError('x')
    })
  })
})
