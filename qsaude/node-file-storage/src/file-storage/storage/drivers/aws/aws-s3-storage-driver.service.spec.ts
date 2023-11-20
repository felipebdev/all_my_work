import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { AwsS3StorageDriverService } from '@app/file-storage/storage/drivers/aws/aws-s3-storage-driver.service'
import { IFile } from '@app/file-storage/storage/interfaces/models/file.interface'
import {
  GetObjectCommand,
  GetObjectCommandOutput,
  PutObjectCommand,
  PutObjectCommandOutput,
  S3Client
} from '@aws-sdk/client-s3'
import { Test } from '@nestjs/testing'
import { createReadStream } from 'fs'
import { mock } from 'jest-mock-extended'

jest.mock('@aws-sdk/client-s3', () => {
  const original = jest.requireActual('@aws-sdk/client-s3')
  return {
    ...original,
    PutObjectCommand: jest.fn(),
    GetObjectCommand: jest.fn()
  }
})
jest.mock('fs', () => {
  const original = jest.requireActual('fs')
  return {
    ...original,
    createReadStream: jest.fn().mockImplementationOnce(() => ({}))
  }
})

describe('AwsS3StorageDriverService', () => {
  let file: IFile
  let putObjectCommandOutput: PutObjectCommandOutput
  let getObjectCommandOutput: GetObjectCommandOutput
  let s3Client: S3Client
  let awsS3StorageDriverService: AwsS3StorageDriverService
  beforeEach(async () => {
    jest.clearAllMocks()
    s3Client = mock<S3Client>()
    file = mock<IFile>({
      filename: 'test.jpg',
      path: 'test/path',
      mimetype: 'image/jpeg',
      size: 123
    })
    putObjectCommandOutput = mock<PutObjectCommandOutput>()
    getObjectCommandOutput = mock<GetObjectCommandOutput>()
    const moduleRef = await Test.createTestingModule({
      imports: [],
      controllers: [],
      providers: [
        {
          provide: S3Client,
          useValue: s3Client
        },
        AwsS3StorageDriverService
      ]
    }).compile()
    awsS3StorageDriverService = moduleRef.get<AwsS3StorageDriverService>(AwsS3StorageDriverService)
  })

  it('should be defined', () => {
    expect(awsS3StorageDriverService).toBeDefined()
  })
  describe('store', () => {
    it('should upload file flow with success', async () => {
      s3Client.send = jest.fn().mockResolvedValueOnce(putObjectCommandOutput)
      const expectedCreateReadStream = Object.assign({}, { ...file })
      const settings = mock<IApplicationSettings>({
        aws: {
          s3: {
            bucket: 'bucket'
          }
        },
        storage: {
          path: 'path'
        }
      })
      const result = await awsS3StorageDriverService.uploadFile(file, settings)
      expect(createReadStream).toHaveBeenCalledTimes(1)
      expect(createReadStream).toHaveBeenCalledWith(expectedCreateReadStream.path)
      expect(PutObjectCommand).toHaveBeenCalledTimes(1)
      expect(PutObjectCommand).toHaveBeenCalledWith({
        Bucket: settings.aws.s3.bucket,
        Key: `${settings.storage.path}/${file.filename}`,
        Body: {},
        ContentLength: file.size,
        ContentType: file.mimetype
      })
      expect(s3Client.send).toHaveBeenCalledTimes(1)
      expect(s3Client.send).toHaveBeenCalledWith(expect.any(PutObjectCommand))
      expect(result).toEqual(putObjectCommandOutput)
    })
    it('should throw a error if client throws', async () => {
      s3Client.send = jest.fn().mockRejectedValueOnce(new Error())
      const settings = mock<IApplicationSettings>()
      await expect(awsS3StorageDriverService.uploadFile(file, settings)).rejects.toThrowError()
    })
  })
  describe('downloadFile', () => {
    it('should execute download file successfully', async () => {
      s3Client.send = jest.fn().mockResolvedValueOnce(getObjectCommandOutput)
      const settings = mock<IApplicationSettings>({
        aws: {
          s3: {
            bucket: 'bucket'
          }
        },
        storage: {
          path: 'path'
        }
      })
      const result = await awsS3StorageDriverService.downloadFile(file.filename, settings)
      expect(GetObjectCommand).toHaveBeenCalledTimes(1)
      expect(GetObjectCommand).toHaveBeenCalledWith({
        Bucket: settings.aws.s3.bucket,
        Key: `${settings.storage.path}/${file.filename}`
      })
      expect(s3Client.send).toHaveBeenCalledTimes(1)
      expect(s3Client.send).toHaveBeenCalledWith(expect.any(GetObjectCommand))
      expect(result).toEqual(getObjectCommandOutput)
    })
    it('should throw a error if client throws', async () => {
      s3Client.send = jest.fn().mockRejectedValueOnce(new Error())
      const settings = mock<IApplicationSettings>()
      await expect(awsS3StorageDriverService.downloadFile(file.filename, settings)).rejects.toThrowError()
    })
  })
})
