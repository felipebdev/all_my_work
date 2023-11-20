import { microsservicesUrlConfig } from '@app/common/configs'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'

describe('MicrosservicesURLsConfig', () => {
  jest.clearAllMocks()
  let configService: ConfigService
  it('should have appropriate app configs', async () => {
    const moduleRef = await Test.createTestingModule({
      imports: [
        ConfigModule.forRoot({
          ignoreEnvFile: false,
          envFilePath: 'test.env',
          expandVariables: true,
          cache: false,
          isGlobal: true
        }),
        ConfigModule.forFeature(microsservicesUrlConfig())
      ],
      controllers: [],
      providers: []
    }).compile()
    configService = moduleRef.get<ConfigService>(ConfigService)
    const config = configService.get('ms')
    expect(config).toHaveProperty('lead')
    expect(config).toHaveProperty('proposal')
    expect(config).toHaveProperty('token')
    expect(config).toHaveProperty('zipCode')
  })
  it('should throw a error if appropriate app configs is not present', async () => {
    const compile = Test.createTestingModule({
      imports: [
        ConfigModule.forRoot({
          ignoreEnvFile: false,
          envFilePath: 'test.env',
          expandVariables: true,
          cache: false,
          isGlobal: true
        }),
        ConfigModule.forFeature(microsservicesUrlConfig())
      ],
      controllers: [],
      providers: []
    }).compile()
    delete process.env.ZIP_CODE_MS_BASE_URL
    await expect(compile).rejects.toThrow()
  })
})
