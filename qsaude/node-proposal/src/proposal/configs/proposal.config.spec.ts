import { proposalConfig } from '@app/proposal/configs'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'

describe('proposalConfig', () => {
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
        ConfigModule.forFeature(proposalConfig())
      ],
      controllers: [],
      providers: []
    }).compile()
    configService = moduleRef.get<ConfigService>(ConfigService)
    const config = configService.get('proposal')
    expect(config).toHaveProperty('initialProposalNumber')
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
        ConfigModule.forFeature(proposalConfig())
      ],
      controllers: [],
      providers: []
    }).compile()
    delete process.env.INITIAL_PROPOSAL_NUMBER
    await expect(compile).rejects.toThrow()
  })
})
