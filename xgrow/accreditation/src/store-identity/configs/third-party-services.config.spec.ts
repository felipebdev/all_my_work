import { externalServicesConfig } from '@app/store-identity/configs/third-party-services.config'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'

describe('ExternalServices', () => {
  let configService: ConfigService
  describe('ExternalServices Config', () => {
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
          ConfigModule.forFeature(externalServicesConfig())
        ],
        controllers: [],
        providers: []
      }).compile()
      configService = moduleRef.get<ConfigService>(ConfigService)
      const thirdPartyConfig = configService.get('external-services')
      expect(thirdPartyConfig).toHaveProperty('nextcode')
      expect(thirdPartyConfig).toHaveProperty('nextcode.baseUrl')
      expect(thirdPartyConfig).toHaveProperty('nextcode.accessToken')
      expect(thirdPartyConfig).toHaveProperty('bigId')
      expect(thirdPartyConfig).toHaveProperty('bigId.baseUrl')
      expect(thirdPartyConfig).toHaveProperty('bigId.accessToken')
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
          ConfigModule.forFeature(externalServicesConfig())
        ],
        controllers: [],
        providers: []
      }).compile()
      delete process.env.NEXTCODE_BASE_URL
      await expect(compile).rejects.toThrow()
    })
  })
})
