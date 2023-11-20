import { databaseConfig } from '@app/common/configs'
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
          ConfigModule.forFeature(databaseConfig())
        ],
        controllers: [],
        providers: []
      }).compile()

      configService = moduleRef.get<ConfigService>(ConfigService)
      const databaseConfigValues = configService.get('database')
      expect(databaseConfigValues).toHaveProperty('type')
      expect(databaseConfigValues).toHaveProperty('host')
      expect(databaseConfigValues).toHaveProperty('port')
      expect(databaseConfigValues).toHaveProperty('username')
      expect(databaseConfigValues).toHaveProperty('password')
      expect(databaseConfigValues).toHaveProperty('name')
      expect(databaseConfigValues).toHaveProperty('synchronize')
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
          ConfigModule.forFeature(databaseConfig())
        ],
        controllers: [],
        providers: []
      }).compile()
      delete process.env.DB_PROPOSAL_TYPE
      await expect(compile).rejects.toThrow()
    })
  })
})
