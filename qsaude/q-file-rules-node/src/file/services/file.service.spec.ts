import { Test, TestingModule } from '@nestjs/testing'
import { getModelToken } from 'nestjs-dynamoose'
import { FileService } from './file.service'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { createMock } from '@golevelup/nestjs-testing'
import { NotFoundException } from '@nestjs/common'

const mock = {
  id: '61ded667-df22-4322-8422-2a1524c9504e'
}

const fileMock = {
  Body: 'any',
  ContentType: 'image/png',
  ContentLength: '1000'
}

// A função 'promise' do S3 é referenciada por default sem retorno
// você deve, então, implementar seus retornos em seus casos de teste de acordo com o contexto
const S3LibMock = {
  upload: jest.fn().mockReturnThis(),
  getObject: jest.fn().mockReturnThis(),
  deleteObject: jest.fn().mockReturnThis(),
  promise: jest.fn()
}

jest.mock('aws-sdk', () => ({
  S3: jest.fn(() => S3LibMock)
}))

describe('FileService', () => {
  let service: FileService

  const MODEL = getModelToken('q-ecomm-files')

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn((val) => {
      switch (val) {
        case 's3.bucket':
          return 'anybucketname'

        case 's3.accessKeyId':
          return 'anyaccesskey'

        case 's3.secretAccessKey':
          return 'anysecret'

        default:
          return 'anyvalue'
      }
    })
  })

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        FileService,
        {
          provide: MODEL,
          useValue: {
            get: jest.fn(() => mock)
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<FileService>(FileService)
  })

  it('should be defined', () => {
    expect(service).toBeDefined()
  })

  it('should return a file', async () => {
    const response = await service.findOne(mock.id)

    expect(typeof response.id).toEqual('string')
    expect(response.id).toEqual(mock.id)
  })

  describe('preview', () => {
    it('should return file from s3 bucket if found', async () => {
      jest.spyOn(S3LibMock, 'promise').mockImplementationOnce(() => {
        return new Promise((res) => res(fileMock))
      })
      const file = await service.preview('anyid')
      expect(S3LibMock.getObject).toBeCalledTimes(1)
      expect(S3LibMock.getObject).toBeCalledWith({ Bucket: 'anybucketname', Key: 'anyid' })
      expect(file).toBe(fileMock)
    })

    it('should throw NotFoundException if file was not found', async () => {
      jest.spyOn(S3LibMock, 'promise').mockImplementationOnce(() => {
        return new Promise((res, rej) => rej('error'))
      })
      await expect(service.preview('fail')).rejects.toThrowError(new NotFoundException(`File #fail was not found`))
      expect(S3LibMock.getObject).toBeCalledTimes(1)
      expect(S3LibMock.getObject).toBeCalledWith({ Bucket: 'anybucketname', Key: 'fail' })
    })
  })
})
