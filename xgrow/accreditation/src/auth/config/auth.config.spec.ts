import { authConfig } from '@app/auth/config'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'
describe('DatabaseConfig', () => {
  let configService: ConfigService
  describe('Database Config', () => {
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
          ConfigModule.forFeature(authConfig())
        ],
        controllers: [],
        providers: []
      }).compile()
      configService = moduleRef.get<ConfigService>(ConfigService)
      const authConfigValues = configService.get('auth')
      expect(authConfigValues).toHaveProperty('jwt')
      expect(authConfigValues).toHaveProperty('jwt.secret')
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
          ConfigModule.forFeature(authConfig())
        ],
        controllers: [],
        providers: []
      }).compile()
      delete process.env.JWT_SECRET
      await expect(compile).rejects.toThrow()
    })
  })
})
