import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { NextcodeService } from '@app/store-identity/services'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { validatedDocumentMock } from '@app/store-identity/services/mocks/nextcode.mock'
import { UnprocessableEntityException } from '@nestjs/common'
import { NextCodeIDExceptionsMessages } from '@app/store-identity/interfaces'

const mockConfigService = createMock<ConfigService>({
  get: jest.fn((val) => {
    switch (val) {
      case 'external-services.nextcode.baseUrl':
        return 'nextcodeurl'

      case 'external-services.nextcode.accessToken':
        return 'nextcodetoken'

      default:
        break
    }
  })
})

const httpServiceMock = { axiosRef: { post: jest.fn(() => ({ data: validatedDocumentMock })) } }

const expectedHeaders = {
  authorization: 'ApiKey nextcodetoken',
  'Content-Type': 'application/json'
}

describe('NextcodeService', () => {
  let service: NextcodeService

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule],
      providers: [
        NextcodeService,
        {
          provide: HttpService,
          useValue: httpServiceMock
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<NextcodeService>(NextcodeService)
  })

  it('controller and service should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('ocr', () => {
    it('should return formatted cpf', async () => {
      const response = await service.ocr('anyname', 'base64file')
      expect(httpServiceMock.axiosRef.post).toBeCalledTimes(1)
      expect(httpServiceMock.axiosRef.post).toBeCalledWith(
        'nextcodeurl/full-ocr/v4',
        {
          base64: {
            anyname: 'base64file'
          }
        },
        { headers: expectedHeaders }
      )
      expect(response).toBe('99999999999')
    })

    it('should throw UnprocessableEntityException with NOCPF', async () => {
      jest.spyOn(httpServiceMock.axiosRef, 'post').mockImplementationOnce(() => ({
        data: {
          ...validatedDocumentMock,
          data: [
            {
              ...validatedDocumentMock.data[0],
              extraction: {
                ...validatedDocumentMock.data[0].extraction,
                person: {
                  ...validatedDocumentMock.data[0].extraction.person,
                  taxId: undefined
                }
              }
            }
          ]
        }
      }))

      await expect(service.ocr('anyname', 'base64file')).rejects.toThrowError(
        new UnprocessableEntityException({
          validate_error: true,
          message: NextCodeIDExceptionsMessages.NOCPF
        })
      )
      expect(httpServiceMock.axiosRef.post).toBeCalledTimes(1)
      expect(httpServiceMock.axiosRef.post).toBeCalledWith(
        'nextcodeurl/full-ocr/v4',
        {
          base64: {
            anyname: 'base64file'
          }
        },
        { headers: expectedHeaders }
      )
    })

    // it('should throw UnprocessableEntityException with DIFFERENT_DOCUMENT', async () => {
    //   jest.spyOn(httpServiceMock.axiosRef, 'post').mockImplementationOnce(() => ({
    //     data: {
    //       ...validatedDocumentMock,
    //       data: [
    //         {
    //           ...validatedDocumentMock.data[0],
    //           extraction: {
    //             ...validatedDocumentMock.data[0].extraction,
    //             person: {
    //               ...validatedDocumentMock.data[0].extraction.person,
    //               taxId: 'any'
    //             }
    //           }
    //         }
    //       ]
    //     }
    //   }))

    //   await expect(service.ocr('anyname', 'base64file')).rejects.toThrowError(
    //     new UnprocessableEntityException({
    //       validate_error: true,
    //       message: NextCodeIDExceptionsMessages.DIFFERENT_DOCUMENT
    //     })
    //   )
    //   expect(httpServiceMock.axiosRef.post).toBeCalledTimes(1)
    //   expect(httpServiceMock.axiosRef.post).toBeCalledWith(
    //     'nextcodeurl/full-ocr/v4',
    //     {
    //       base64: {
    //         anyname: 'base64file'
    //       }
    //     },
    //     { headers: expectedHeaders }
    //   )
    // })
  })
})
