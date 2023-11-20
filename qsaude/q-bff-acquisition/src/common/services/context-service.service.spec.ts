import { Test, TestingModule } from '@nestjs/testing'
import { ContextService } from '@app/common/services'
import { createMock } from '@golevelup/nestjs-testing'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { cacheConfig } from '@app/common/configs'
import { CACHE_MANAGER } from '@nestjs/common'
import { Cache } from 'cache-manager'

describe('ContextServiceService', () => {
  let service: ContextService
  let configService: ConfigService

  const cacheManagerMock: Cache = createMock<Cache>({
    get: jest.fn().mockImplementation(async () => Promise.resolve()),
    set: jest.fn().mockImplementation(async () => Promise.resolve()),
    del: jest.fn().mockImplementation(async () => Promise.resolve())
  });

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
        }),
        ConfigModule.forFeature(cacheConfig())
      ],
      providers: [
        {
          provide: CACHE_MANAGER,
          useValue: cacheManagerMock
        },
        ContextService
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(configService)
      .compile()

    service = module.get<ContextService>(ContextService)
  })

  it('should be defined', () => {
    expect(service).toBeDefined()
  })

  describe('set()', () => {
    it('should work when called with valid params', async () => {
      await expect(service.set('anykey', 'y')).resolves.not.toThrow()
    })

    it('should call cache manager correctly', async () => {
      await service.set('anykey', 'x')
      expect(cacheManagerMock.set).toBeCalledTimes(1)
      expect(cacheManagerMock.set).toBeCalledWith('anykey', 'x')
    })
  })

  describe('get()', () => {
    it('should work when called with valid params', async () => {
      await expect(service.get('key')).resolves.not.toThrow()
    })

    it('should call cache manager correctly', async () => {
      await service.get('anykey')
      expect(cacheManagerMock.get).toBeCalledTimes(1)
      expect(cacheManagerMock.get).toBeCalledWith('anykey')
    })
  })

  describe('delete()', () => {
    it('should work when called with valid params', async () => {
      await expect(service.delete('anykey')).resolves.not.toThrow()
    })

    it('should call cache manager correctly', async () => {
      await service.delete('anykey')
      expect(cacheManagerMock.del).toBeCalledTimes(1)
      expect(cacheManagerMock.del).toBeCalledWith('anykey')
    })
  })
})
