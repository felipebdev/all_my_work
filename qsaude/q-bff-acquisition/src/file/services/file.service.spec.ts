import { Test, TestingModule } from '@nestjs/testing'
import { FileService } from './file.service'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'

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

describe('FileService', () => {
  let service: FileService
  let httpService: HttpService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'any-url')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        FileService,
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              get: jest.fn(() => mock),
              post: jest.fn(() => mock)
            }
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

  describe('parametrizationFile', () => {
    it('should return person correctly when found', async () => {
      const [response] = await service.parametrizationFile(params)
      expect(httpService.axiosRef.get).toBeCalledTimes(1)
      expect(httpService.axiosRef.get).toBeCalledWith(
        `/file?origin=${params.origin}&idProposal=${params.idProposal}&idPerson=${params.idPerson}`
      )
      expect(response.id).toBe(mock.id)
    })
  })
})
