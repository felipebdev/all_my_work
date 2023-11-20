import { Test, TestingModule } from '@nestjs/testing'
import { ContactController } from '@app/contact/controllers/contact.controller'
import { AbstractContactService } from '@app/contact/services/abstract/contact.service.abstract'
import { ContactUuid } from '@app/contact/dtos'

const contactsResponseMock = [
  {
    idContact: 'any',
    type: 'e',
    value: 'any@gmail.com',
    refType: 'P',
    refId: 'anyid'
  },
  {
    idContact: 'any',
    type: 'c',
    value: '19982867379',
    refType: 'P',
    refId: 'anyid'
  }
]

const contactsInput: ContactUuid = {
  cellphone: '19982867399',
  email: 'any@gmail.com',
  refId: 'anyid'
}

describe('ContactController', () => {
  let controller: ContactController
  let contactService: AbstractContactService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        {
          provide: AbstractContactService,
          useValue: {
            createPersonContacts: jest.fn(() => contactsResponseMock)
          }
        }
      ],
      controllers: [ContactController]
    }).compile()
    controller = module.get<ContactController>(ContactController)
    contactService = module.get<AbstractContactService>(AbstractContactService)
  })

  it('controller should be defined', () => {
    expect(controller).toBeDefined()
  })

  it('service should be defined', () => {
    expect(contactService).toBeDefined()
  })

  describe('createPersonContacts', () => {
    it('should return created contacts when ContactService works correctly', async () => {
      const response = await controller.createPersonContacts(contactsInput)
      expect(contactService.createPersonContacts).toBeCalledTimes(1)
      expect(contactService.createPersonContacts).toBeCalledWith(contactsInput)
      expect(response).toBe(contactsResponseMock)
    })

    it('throw an error when ContactService fails', async () => {
      jest.spyOn(contactService, 'createPersonContacts').mockImplementation(() => {
        throw new Error('x')
      })
      await expect(controller.createPersonContacts(contactsInput)).rejects.toThrowError('x')
      expect(contactService.createPersonContacts).toBeCalledTimes(1)
      expect(contactService.createPersonContacts).toBeCalledWith(contactsInput)
    })
  })
})
