import { databaseConfig as databaseConfigRegister } from '@app/common/configs'
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
          ConfigModule.forFeature(databaseConfigRegister())
        ],
        controllers: [],
        providers: []
      }).compile()

      configService = moduleRef.get<ConfigService>(ConfigService)
      const databaseConfig = configService.get('database')

      if (databaseConfig) {
        expect(databaseConfig).toHaveProperty('accessKeyId')
        expect(databaseConfig).toHaveProperty('secretAccessKey')
        expect(databaseConfig).toHaveProperty('region')
      }
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
          ConfigModule.forFeature(databaseConfigRegister())
        ],
        controllers: [],
        providers: []
      }).compile()
      delete process.env.DB_FILE_REGION
      await expect(compile).rejects.toThrow()
    })
  })
})
