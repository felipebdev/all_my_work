import { Test, TestingModule } from '@nestjs/testing'
import { GCPStorageService } from '@app/common/services'
import { createMock } from '@golevelup/nestjs-testing'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { File } from '@app/common/interfaces'
import { BadRequestException } from '@nestjs/common'

const fileMock = {
  save: jest.fn(),
  delete: jest.fn(),
  metadata: { any: 'value' }
}
const bucketMock = {
  file: jest.fn(() => fileMock)
}

const gCloudStorageMock = {
  bucket: jest.fn(() => bucketMock)
}

jest.mock('@google-cloud/storage', () => ({
  Storage: jest.fn(() => gCloudStorageMock)
}))

const file: File = {
  fieldname: 'string',
  originalname: 'string',
  encoding: 'string',
  mimetype: 'jpg',
  size: 2,
  destination: 'string',
  filename: 'string',
  path: 'string',
  buffer: Buffer.from('anyanyany')
}

describe('GCPStorageService', () => {
  let service: GCPStorageService
  let configService: ConfigService

  beforeEach(async () => {
    jest.clearAllMocks()
    configService = createMock<ConfigService>()

    const module: TestingModule = await Test.createTestingModule({
      imports: [
        ConfigModule.forRoot({
          ignoreEnvFile: false,
          envFilePath: 'test.env',
          expandVariables: true,
          cache: false,
          isGlobal: true
        })
      ],
      providers: [GCPStorageService]
    })
      .overrideProvider(ConfigService)
      .useValue(configService)
      .compile()

    service = module.get<GCPStorageService>(GCPStorageService)
  })

  it('should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('uploadFile', () => {
    it('should return metadata', async () => {
      const response = await service.uploadFile(file, 'any')
      expect(bucketMock.file).toBeCalledTimes(1)
      expect(bucketMock.file).toBeCalledWith(expect.any(String))
      expect(fileMock.save).toBeCalledTimes(1)
      expect(fileMock.save).toBeCalledWith(file.buffer, { contentType: 'jpg' })
      expect(response).toStrictEqual({
        any: 'value'
      })
    })

    it('should throw BadRequestException', async () => {
      jest.spyOn(fileMock, 'save').mockImplementation(() => {
        throw new Error('anyerror')
      })

      await expect(service.uploadFile(file, 'any')).rejects.toThrowError(new BadRequestException('anyerror'))
    })

    it('should throw BadRequestException without message', async () => {
      jest.spyOn(fileMock, 'save').mockImplementation(() => {
        throw undefined
      })

      await expect(service.uploadFile(file, 'any')).rejects.toThrowError(new BadRequestException(undefined))
    })
  })

  describe('removeFile', () => {
    it('should delete file', async () => {
      const response = await service.removeFile('any')
      expect(bucketMock.file).toBeCalledTimes(1)
      expect(bucketMock.file).toBeCalledWith(expect.any(String))
      expect(fileMock.delete).toBeCalledTimes(1)
      expect(response).toBeUndefined()
    })

    it('should throw BadRequestException', async () => {
      jest.spyOn(fileMock, 'delete').mockImplementation(() => {
        throw new Error('anyerror')
      })

      await expect(service.removeFile('any')).rejects.toThrowError(new BadRequestException('anyerror'))
    })

    it('should throw BadRequestException without message', async () => {
      jest.spyOn(fileMock, 'delete').mockImplementation(() => {
        throw undefined
      })

      await expect(service.removeFile('any')).rejects.toThrowError(new BadRequestException(undefined))
    })
  })
})
