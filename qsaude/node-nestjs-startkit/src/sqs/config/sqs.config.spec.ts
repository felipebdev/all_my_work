import { sqsConfig } from '@app/sqs/config'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'

describe('appConfig', () => {
  jest.clearAllMocks()
  let sut: ConfigService
  it('should have appropriate sqs configs', async () => {
    const moduleRef = await Test.createTestingModule({
      imports: [
        ConfigModule.forRoot({
          ignoreEnvFile: false,
          envFilePath: 'test.env',
          expandVariables: true,
          cache: false,
          isGlobal: true
        }),
        ConfigModule.forFeature(sqsConfig())
      ],
      controllers: [],
      providers: []
    }).compile()
    sut = moduleRef.get<ConfigService>(ConfigService)
    const configs = sut.get('sqs')
    expect(configs).toHaveProperty('accountNumber')
    expect(configs).toHaveProperty('region')
    expect(configs).toHaveProperty('endpoint')
    expect(configs.credentials).toHaveProperty('accessKeyId')
    expect(configs.credentials).toHaveProperty('secretAccessKey')
    expect(configs.consumer).toHaveProperty('waitTimeSeconds')
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
        ConfigModule.forFeature(sqsConfig())
      ],
      controllers: [],
      providers: []
    }).compile()
    delete process.env.AWS_SQS_ACCOUNT_ID
    await expect(compile).rejects.toThrow()
  })
})
