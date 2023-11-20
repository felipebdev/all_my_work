import { Test, TestingModule } from '@nestjs/testing'
import { FileService } from './file.service'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { ParametrizationService } from '@app/parametrization/services/parametrization.service'
import { CreateFileDto } from '@app/file/dtos'
import FormData from 'form-data'
import { Readable } from 'stream'
import { InternalServerErrorException, StreamableFile } from '@nestjs/common'

const mock = {
  origin: 'q-file-rules-node',
  fileSize: 5399,
  idProposal: '3b7fc8fc-2895-46ce-ac25-45593b794b4f',
  idPerson: '53eeb79b-ce9a-484d-b56b-1728166fe9dc',
  fileOriginalname: 'images.jpg',
  fileMimetype: 'image/jpeg',
  fileType: '2',
  id: '7449fce9-1101-4ec1-ba88-79add15db8c9'
}

const params = {
  origin: 'q-file-rules-node',
  idProposal: '3b7fc8fc-2895-46ce-ac25-45593b794b4f',
  idPerson: '53eeb79b-ce9a-484d-b56b-1728166fe9dc',
  beneficiaryType: 'titular',
  saleType: 'pme'
}

const createFileInput: CreateFileDto = {
  fileType: 'any',
  idPerson: 'any',
  idProposal: 'any'
}

const fileInput: Express.Multer.File = {
  filename: 'any-name',
  path: 'any-path',
  buffer: Buffer.from('any'),
  destination: 'any-dest',
  fieldname: 'any-field-name',
  mimetype: 'image/png',
  originalname: 'any-name',
  size: 1000,
  stream: new Readable(),
  encoding: ''
}

describe('FileService', () => {
  let service: FileService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn((val) => {
      switch (val) {
        case 'ms.file':
          return 'filemsurl'
        case 'ms.fileOrigin':
          return 'pme'
        default:
          return 'unexpected-resource'
      }
    })
  })

  const axiosMock = {
    axiosRef: {
      get: jest.fn((url) => {
        switch (url) {
          case 'filemsurl/file/preview/anyid':
            return { data: Buffer.from('any'), headers: { 'content-type': 'any' } }
          default:
            return 'unexpected-resource'
        }
      }),
      post: jest.fn((url) => {
        switch (url) {
          case 'filemsurl/file':
            return { data: mock }
          default:
            return 'unexpected-resource'
        }
      }),
      delete: jest.fn()
    }
  }

  beforeEach(async () => {
    jest.clearAllMocks()
    jest.clearAllTimers()
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        FileService,
        {
          provide: HttpService,
          useValue: axiosMock
        },
        {
          provide: ParametrizationService,
          useValue: {
            getParametrization: jest.fn()
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<FileService>(FileService)
    httpService = module.get<HttpService>(HttpService)
  })

  it('services should be defined', () => {
    expect(service).toBeDefined()
    expect(httpService).toBeDefined()
  })

  // teste quebrando:
  // describe('parametrizationFile', () => {
  //   it('should return person correctly when found', async () => {
  //     const [response] = await service.parametrizationFile(params)
  //     expect(httpService.axiosRef.get).toBeCalledTimes(1)
  //     expect(httpService.axiosRef.get).toBeCalledWith(
  //       `/file?origin=${params.origin}&idProposal=${params.idProposal}&idPerson=${params.idPerson}`
  //     )
  //     expect(response.id).toBe(mock.id)
  //   })
  // })

  describe('create', () => {
    it('should return created file if axios req works correctly ', async () => {
      const file = await service.create(createFileInput, fileInput)
      expect(httpService.axiosRef.post).toBeCalledTimes(1)
      expect(httpService.axiosRef.post).toBeCalledWith('filemsurl/file', expect.anything(), expect.anything())
      expect(file).toBe(mock)
    })

    it('should throw InternalServerErrorException with details when req fails', async () => {
      jest.spyOn(httpService.axiosRef, 'post').mockImplementationOnce(() => {
        throw { anydetails: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.create(createFileInput, fileInput)).rejects.toThrowError(
        new InternalServerErrorException({ anydetails: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } })
      )
    })
  })

  describe('preview', () => {
    it('should return file if axios req works correctly ', async () => {
      const file = await service.preview('anyid')
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith('filemsurl/file/preview/anyid', { responseType: 'arraybuffer' })
      expect(file).toHaveProperty('stream')
      expect(file).toHaveProperty('options')
      expect(file.options).toStrictEqual({ type: 'any', length: expect.anything() })
      expect(file).toBeInstanceOf(StreamableFile)
    })

    it('should throw InternalServerErrorException with details when req fails', async () => {
      jest.spyOn(httpService.axiosRef, 'get').mockImplementationOnce(() => {
        throw { details: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.preview('anyid')).rejects.toThrowError(
        new InternalServerErrorException({ details: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } })
      )
    })
  })

  describe('delete', () => {
    it('should delete file if axios req works correctly ', async () => {
      const deleteResponse = await service.delete('anyid')
      expect(httpService.axiosRef.delete).toBeCalledTimes(1)
      expect(httpService.axiosRef.delete).toBeCalledWith('filemsurl/file/anyid')
      expect(deleteResponse).toBeUndefined()
    })

    it('should throw InternalServerErrorException with details when req fails', async () => {
      jest.spyOn(httpService.axiosRef, 'delete').mockImplementationOnce(() => {
        throw { details: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } }
      })
      await expect(service.delete('anyid')).rejects.toThrowError(
        new InternalServerErrorException({ details: { status: 400, data: { message: 'ID XYZ WAS NOT FOUND' } } })
      )
    })
  })
})
