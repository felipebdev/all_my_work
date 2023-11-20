import { googleStorageConfig } from '@app/common/config'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'
describe('GCPStorageConfig', () => {
  let configService: ConfigService
  describe('GCPStorage Config', () => {
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
          ConfigModule.forFeature(googleStorageConfig())
        ],
        controllers: [],
        providers: []
      }).compile()
      configService = moduleRef.get<ConfigService>(ConfigService)
      const databaseConfig = configService.get('gcps')
      expect(databaseConfig).toHaveProperty('bucketName')
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
          ConfigModule.forFeature(googleStorageConfig())
        ],
        controllers: [],
        providers: []
      }).compile()
      delete process.env.GCP_STORAGE_BUCKET_NAME
      await expect(compile).rejects.toThrow()
    })
  })
})
