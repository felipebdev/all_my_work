import { PersonEntity } from '@app/person/entities'
import { PersonService } from '@app/person/services/person.service'
import { Test, TestingModule } from '@nestjs/testing'
import { getRepositoryToken } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { Gender } from '@app/person/interfaces/enums'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'

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

describe('PersonService', () => {
  let service: PersonService
  let personRepository: Repository<PersonEntity>

  const PERSON_REPOSITORY_TOKEN = getRepositoryToken(PersonEntity)

  beforeEach(async () => {
    jest.clearAllTimers()
    jest.clearAllMocks()
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        PersonService,
        {
          provide: PERSON_REPOSITORY_TOKEN,
          useValue: {
            create: jest.fn(() => personMock),
            save: jest.fn(() => personMock),
            findOne: jest.fn(() => personMock),
            preload: jest.fn((args) => ({ ...personMock, ...args }))
          }
        }
      ]
    }).compile()

    service = module.get<PersonService>(PersonService)
    personRepository = module.get<Repository<PersonEntity>>(PERSON_REPOSITORY_TOKEN)
  })

  it('service should be defined', () => {
    expect(service).toBeDefined()
  })

  it('personRepository should be defined', () => {
    expect(personRepository).toBeDefined()
  })

  describe('createOrUpdate', () => {
    it('should use personRepository.save method correctly', async () => {
      const person = await service.createOrUpdate(personMock)
      expect(personRepository.create).toBeCalledTimes(1)
      expect(personRepository.save).toBeCalledTimes(1)
      expect(personRepository.create).toBeCalledWith(personMock)
      expect(personRepository.save).toBeCalledWith(personMock)
      expect(person).toBe(personMock)
    })

    it('should throw InternalServerErrorException if .save or .create fails', async () => {
      jest.spyOn(personRepository, 'create').mockImplementationOnce(() => {
        throw { any: 'any' }
      })
      await expect(service.createOrUpdate(personMock)).rejects.toThrowError(
        new InternalServerErrorException({ any: 'any' })
      )
      expect(personRepository.create).toBeCalledTimes(1)
      expect(personRepository.create).toBeCalledWith(personMock)
      expect(personRepository.save).toBeCalledTimes(0)
    })
  })

  describe('findOne', () => {
    it('should return person correctly when found', async () => {
      const response = await service.findOne('any')
      expect(personRepository.findOne).toBeCalledTimes(1)
      expect(personRepository.findOne).toBeCalledWith({
        where: {
          idPerson: 'any'
        }
      })
      expect(response).toBe(personMock)
    })

    it('should throw NotFoundException when Person was not found', async () => {
      jest.spyOn(personRepository, 'findOne').mockReturnValue(null)
      await expect(service.findOne('any')).rejects.toThrowError(new NotFoundException(`Person #any was not found`))
      expect(personRepository.findOne).toBeCalledTimes(1)
      expect(personRepository.findOne).toBeCalledWith({
        where: {
          idPerson: 'any'
        }
      })
    })
  })

  describe('update', () => {
    it('should return person correctly when updated', async () => {
      const response = await service.update('anyid', { name: 'newname' })
      expect(personRepository.preload).toBeCalledTimes(1)
      expect(personRepository.preload).toBeCalledWith({ idPerson: 'anyid', name: 'newname' })
      expect(personRepository.save).toBeCalledTimes(1)
      expect(personRepository.save).toBeCalledWith({
        ...personMock,
        idPerson: 'anyid',
        name: 'newname'
      })
      expect(response).toBe(personMock)
    })

    it('should throw InternalServerErrorException when any method fails', async () => {
      jest.spyOn(personRepository, 'preload').mockImplementationOnce(() => {
        throw { any: 'any' }
      })
      await expect(service.update('anyid', { name: 'newname' })).rejects.toThrowError(
        new InternalServerErrorException({ any: 'any' })
      )
      expect(personRepository.preload).toBeCalledTimes(1)
      expect(personRepository.preload).toBeCalledWith({ idPerson: 'anyid', name: 'newname' })
      expect(personRepository.save).toBeCalledTimes(0)
    })
  })
})
