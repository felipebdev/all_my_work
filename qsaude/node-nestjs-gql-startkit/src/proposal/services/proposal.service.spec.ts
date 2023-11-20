import { TokenEnum } from '@app/token/interfaces/enum/token.enum'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { ProposalService } from './proposal.service'
import { PersonService } from '../../person/services/person.service'
import { TokenService } from '../../token/services/token.service'
import { Person } from '../../person/models/person.model'
import { Proposal } from '@app/proposal/models/proposal.model'
import { ProposalRole } from '../models/proposal-role.model'
import { ProposalRoleEnum } from '../interfaces/enums/proposal.enum'

const currDate = new Date()

const personMock: Person = {
  idPerson: 'anypersonid',
  name: 'anyname'
}

const proposalMock: Proposal = {
  idProposal: 'anyproposalid'
}

const proposalRoleMock: ProposalRole = {
  createdAt: currDate,
  updatedAt: currDate,
  idPerson: 'anypersonid',
  idProposal: 'anyproposalid',
  idProposalRole: 'anyproposalroleid',
  role: ProposalRoleEnum.LegalRepresentative
}

const personInputMock = {
  cpf: '00000000000',
  name: 'anyname',
  email: 'email@email.com',
  cellphone: '11999999999'
}

const tokenValidationInputMock = {
  cpf: '00000000000',
  token: '2374',
  type: TokenEnum.EMAIL,
  value: 'email@gmail.com'
}

const proposalInputMock = {
  idLead: 'anyid'
}

describe('ProposalService', () => {
  let service: ProposalService
  let httpService: HttpService
  let tokenService: TokenService
  let personService: PersonService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'proposalmsurl')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        ProposalService,
        {
          provide: PersonService,
          useValue: {
            create: jest.fn(() => personMock),
            createPersonContacts: jest.fn()
          }
        },
        {
          provide: TokenService,
          useValue: {
            check: jest.fn(() => true)
          }
        },
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              post: jest.fn((url) => {
                switch (url) {
                  case 'proposalmsurl/proposal':
                    return { data: { ...proposalMock } }

                  case 'proposalmsurl/proposal-role':
                    return { data: { ...proposalRoleMock } }

                  default:
                    return { data: {} }
                }
              })
            }
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<ProposalService>(ProposalService)
    httpService = module.get<HttpService>(HttpService)
    tokenService = module.get<TokenService>(TokenService)
    personService = module.get<PersonService>(PersonService)
  })

  it('services should be defined', () => {
    expect(service).toBeDefined()
    expect(httpService).toBeDefined()
    expect(tokenService).toBeDefined()
    expect(personService).toBeDefined()
  })

  describe('createLegalRepresentative', () => {
    it('should create legal rep if nothing fails', async () => {
      const response = await service.createLegalRepresentative({
        person: personInputMock,
        proposal: proposalInputMock,
        tokenValidation: tokenValidationInputMock
      })
      const { email, cellphone, ...personRest } = personInputMock
      expect(tokenService.check).toBeCalledTimes(1)
      expect(tokenService.check).toBeCalledWith(tokenValidationInputMock)
      expect(personService.create).toBeCalledTimes(1)
      expect(personService.create).toBeCalledWith(personRest)
      expect(personService.createPersonContacts).toBeCalledTimes(1)
      expect(personService.createPersonContacts).toBeCalledWith({
        email: personInputMock.email,
        cellphone: personInputMock.cellphone,
        refId: proposalRoleMock.idProposalRole
      })
      expect(httpService.axiosRef.post).toBeCalledTimes(2)
      expect(httpService.axiosRef.post).toHaveBeenNthCalledWith(1, 'proposalmsurl/proposal', proposalInputMock)
      expect(httpService.axiosRef.post).toHaveBeenNthCalledWith(2, 'proposalmsurl/proposal-role', {
        idPerson: personMock.idPerson,
        idProposal: proposalMock.idProposal,
        role: ProposalRoleEnum.LegalRepresentative
      })
      expect(response).toStrictEqual({
        person: {
          ...personMock,
          email: personInputMock.email,
          cellphone: personInputMock.cellphone
        },
        token: true,
        proposal: proposalMock,
        proposalRole: proposalRoleMock
      })
    })
  })
})
