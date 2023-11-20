import { ConfigModule } from '@app/file-storage/config/config.module'
import { ConfigModule as NestConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'
import { authConfig } from './auth.config'

describe('AuthConfig', () => {
  let configService: ConfigService
  it('should have the appropriate settings', async () => {
    const moduleRef = await Test.createTestingModule({
      imports: [ConfigModule, NestConfigModule.forFeature(authConfig)],
      controllers: [],
      providers: []
    }).compile()

    configService = moduleRef.get<ConfigService>(ConfigService)
    const configs = configService.get('auth')
    expect(configs.jwt).toHaveProperty('jwksUri')
    expect(configs.jwt).toHaveProperty('audience')
    expect(configs.jwt).toHaveProperty('issuer')
  })
  it('should throw a error if appropriate setting is not present', async () => {
    delete process.env.AUTH_JWKS_URI_PJ
    const compile = Test.createTestingModule({
      imports: [ConfigModule, NestConfigModule.forFeature(authConfig)],
      controllers: [],
      providers: []
    }).compile()
    await expect(compile).rejects.toThrow()
  })
})
