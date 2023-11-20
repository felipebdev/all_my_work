import { FileService } from '@app/file/services/file.service'
import { Test, TestingModule } from '@nestjs/testing'
import { FileResolver } from './file.resolver'

describe('FileResolver', () => {
  let resolver: FileResolver

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        FileResolver,
        {
          provide: FileService,
          useValue: {
            parametrizationFile: jest.fn(() => ({}))
          }
        }
      ]
    }).compile()

    resolver = module.get<FileResolver>(FileResolver)
  })

  it('should be defined', () => {
    expect(resolver).toBeDefined()
  })
})
