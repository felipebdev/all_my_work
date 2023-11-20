import { externalMSsConfig } from '@app/zip-code/configs'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'

describe('ExternalConfig', () => {
  let configService: ConfigService
  describe('External Microsservices Config', () => {
    it('should have the appropriate settings', async () => {
      const moduleRef = await Test.createTestingModule({
        imports: [
          ConfigModule.forRoot({
            ignoreEnvFile: false,
            envFilePath: 'test.env',
            expandVariables: true,
            cache: false,
            isGlobal: true
          }),
          ConfigModule.forFeature(externalMSsConfig())
        ],
        controllers: [],
        providers: []
      }).compile()

      configService = moduleRef.get<ConfigService>(ConfigService)
      const externalMSConfigValues = configService.get('external')
      expect(externalMSConfigValues).toHaveProperty('viaCep')
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
          ConfigModule.forFeature(externalMSsConfig())
        ],
        controllers: [],
        providers: []
      }).compile()
      delete process.env.VIA_CEP_BASE_URL
      await expect(compile).rejects.toThrow()
    })
  })
})
