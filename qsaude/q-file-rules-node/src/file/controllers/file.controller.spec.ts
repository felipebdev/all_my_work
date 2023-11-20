import { ConfigModule } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { FileService } from '../services/file.service'
import { FileController } from './file.controller'
import { StreamableFile } from '@nestjs/common'

const mock = {
  id: '61ded667-df22-4322-8422-2a1524c9504e'
}

const fileMock = {
  Body: Buffer.from('any'),
  ContentType: 'image/png',
  ContentLength: '1000'
}

describe('FileController', () => {
  let controller: FileController
  let service: FileService

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule],
      controllers: [FileController],
      providers: [
        {
          provide: FileService,
          useValue: {
            findOne: jest.fn(() => mock),
            preview: jest.fn(() => fileMock)
          }
        }
      ]
    }).compile()

    controller = module.get<FileController>(FileController)
    service = module.get<FileService>(FileService)
  })

  it('controller and service should be defined', () => {
    expect(controller).toBeDefined()
    expect(service).toBeDefined()
  })

  it('should return a file', async () => {
    const response = await controller.findOne(mock.id)

    expect(typeof response.id).toEqual('string')
    expect(response.id).toEqual(mock.id)
  })

  describe('preview', () => {
    it('should return and instantiate ReadableFile from file.Body', async () => {
      const response = await controller.previewFile('anyid')
      const spyOnService = jest.spyOn(service, 'preview')
      expect(spyOnService).toBeCalledTimes(1)
      expect(spyOnService).toBeCalledWith('anyid')
      expect(response).toHaveProperty('stream')
      expect(response).toHaveProperty('options')
      expect(response.options).toStrictEqual({ type: 'image/png', length: '1000' })
      expect(response).toBeInstanceOf(StreamableFile)
    })
  })
})
