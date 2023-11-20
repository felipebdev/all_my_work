import { CreateFileDto, FileDto } from '@app/file/dtos'
import { FileService } from '@app/file/services'
import { createMock } from '@golevelup/nestjs-testing'
import { StreamableFile } from '@nestjs/common'
import { Test, TestingModule } from '@nestjs/testing'
import { FileController } from './file.controller'

describe('FileController', () => {
  let controller: FileController
  let service: FileService

  const fileInputMock: Express.Multer.File = createMock<Express.Multer.File>()
  const streamableMock: StreamableFile = createMock<StreamableFile>()
  const fileInputDto = createMock<CreateFileDto>()
  const fileResponse = createMock<FileDto>()

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      controllers: [FileController],
      providers: [
        {
          provide: FileService,
          useValue: {
            create: jest.fn(() => fileResponse),
            preview: jest.fn(() => streamableMock),
            delete: jest.fn()
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

  describe('create', () => {
    it('should return service response', async () => {
      const response = await controller.create(fileInputMock, fileInputDto)
      expect(service.create).toBeCalledTimes(1)
      expect(service.create).toBeCalledWith(fileInputDto, fileInputMock)
      expect(response).toBe(fileResponse)
    })
  })
  describe('preview', () => {
    it('should return service response', async () => {
      const response = await controller.preview({ id: 'anyid' })
      expect(service.preview).toBeCalledTimes(1)
      expect(service.preview).toBeCalledWith('anyid')
      expect(response).toBe(streamableMock)
    })
  })
  describe('delete', () => {
    it('should return service response', async () => {
      const response = await controller.delete({ id: 'anyid' })
      expect(service.delete).toBeCalledTimes(1)
      expect(service.delete).toBeCalledWith('anyid')
      expect(response).toBeUndefined()
    })
  })
})
