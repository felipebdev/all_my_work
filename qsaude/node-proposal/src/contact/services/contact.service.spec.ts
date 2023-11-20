import { Test, TestingModule } from '@nestjs/testing'
import { getRepositoryToken } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ContactService } from '@app/contact/services/contact.service'
import { ContactEntity } from '@app/contact/entities'
import { ContactUuid } from '@app/contact/dtos'
import { ContactType } from '@app/contact/interfaces/contact.enum'
import { ContactRefType } from '../interfaces/contact.enum'
import { ContactFilter } from '@app/contact/interfaces/contact.interface'

const contactsResponseMock = [
  {
    idContact: 'any',
    type: ContactType.email,
    value: 'any@gmail.com',
    refType: ContactRefType.person,
    refId: 'anyid'
  },
  {
    idContact: 'any',
    type: ContactType.cellphone,
    value: '19982867379',
    refType: ContactRefType.person,
    refId: 'anyid'
  }
]

const createContactMock = {
  type: ContactType.cellphone,
  value: '19982867379',
  refType: ContactRefType.person,
  refId: 'anyid'
}

const contactsInput: ContactUuid = {
  cellphone: '19982867399',
  email: 'any@gmail.com',
  refId: 'anyid'
}

const findOneByMock: ContactFilter = {
  refId: 'anyid',
  refType: ContactRefType.person,
  type: ContactType.address
}

describe('ContactService', () => {
  let service: ContactService
  let contactRepository: Repository<ContactEntity>

  const CONTACT_REPOSITORY_TOKEN = getRepositoryToken(ContactEntity)

  beforeEach(async () => {
    jest.clearAllTimers()
    jest.clearAllMocks()
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        ContactService,
        {
          provide: CONTACT_REPOSITORY_TOKEN,
          useValue: {
            create: jest.fn((args) => args),
            save: jest.fn(() => contactsResponseMock),
            findOneBy: jest.fn((args) => ({ ...args, idContact: 'anyid' }))
          }
        }
      ]
    }).compile()

    service = module.get<ContactService>(ContactService)
    contactRepository = module.get<Repository<ContactEntity>>(CONTACT_REPOSITORY_TOKEN)
  })

  it('service should be defined', () => {
    expect(service).toBeDefined()
  })

  it('contactRepository should be defined', () => {
    expect(contactRepository).toBeDefined()
  })

  describe('createPersonContacts', () => {
    it('should create email and cellphone contacts if contactRepository .save and .create works fine', async () => {
      const response = await service.createPersonContacts(contactsInput)
      expect(contactRepository.create).toHaveBeenNthCalledWith(1, {
        type: ContactType.email,
        value: contactsInput.email,
        refType: ContactRefType.proposalRole,
        refId: contactsInput.refId
      })

      expect(contactRepository.create).toHaveBeenNthCalledWith(2, {
        type: ContactType.cellphone,
        value: contactsInput.cellphone,
        refType: ContactRefType.proposalRole,
        refId: contactsInput.refId
      })

      expect(contactRepository.save).toBeCalledWith([
        {
          type: ContactType.email,
          value: contactsInput.email,
          refType: ContactRefType.proposalRole,
          refId: contactsInput.refId
        },
        {
          type: ContactType.cellphone,
          value: contactsInput.cellphone,
          refType: ContactRefType.proposalRole,
          refId: contactsInput.refId
        }
      ])

      expect(response).toBe(contactsResponseMock)
    })

    it('should throw InternalServerErrorException if any method fails', async () => {
      jest.spyOn(contactRepository, 'save').mockImplementationOnce(() => {
        throw { any: 'any' }
      })

      await expect(service.createPersonContacts(contactsInput)).rejects.toThrowError(
        new InternalServerErrorException({ any: 'any' })
      )
    })
  })

  describe('create', () => {
    it('should use contactRepository.create and .save method correctly', async () => {
      const contact = await service.create(createContactMock)
      expect(contactRepository.create).toBeCalledTimes(1)
      expect(contactRepository.save).toBeCalledTimes(1)
      expect(contactRepository.create).toBeCalledWith(createContactMock)
      expect(contactRepository.save).toBeCalledWith(createContactMock)
      expect(contact).toBe(contactsResponseMock)
    })
  })

  describe('findOneBy', () => {
    it('should return contact correctly when found', async () => {
      const response = await service.findOneBy(findOneByMock)
      expect(contactRepository.findOneBy).toBeCalledTimes(1)
      expect(contactRepository.findOneBy).toBeCalledWith(findOneByMock)
      expect(response).toStrictEqual({
        ...findOneByMock,
        idContact: 'anyid'
      })
    })

    it('should throw NotFoundException when Contact was not found', async () => {
      jest.spyOn(contactRepository, 'findOneBy').mockReturnValue(null)
      await expect(service.findOneBy(findOneByMock)).rejects.toThrowError(
        new NotFoundException(`Contact was not found`)
      )
      expect(contactRepository.findOneBy).toBeCalledTimes(1)
      expect(contactRepository.findOneBy).toBeCalledWith(findOneByMock)
    })
  })
})
