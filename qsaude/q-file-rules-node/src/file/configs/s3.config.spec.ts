import { s3Config as s3ConfigRegistration } from '@app/file/configs'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'

describe('S3Config', () => {
  let configService: ConfigService
  describe('S3 Config', () => {
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
          ConfigModule.forFeature(s3ConfigRegistration())
        ],
        controllers: [],
        providers: []
      }).compile()

      configService = moduleRef.get<ConfigService>(ConfigService)
      const s3Config = configService.get('s3')

      if (s3Config) {
        expect(s3Config).toHaveProperty('accessKeyId')
        expect(s3Config).toHaveProperty('secretAccessKey')
        expect(s3Config).toHaveProperty('bucket')
        expect(s3Config).toHaveProperty('fileSize')
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
          ConfigModule.forFeature(s3ConfigRegistration())
        ],
        controllers: [],
        providers: []
      }).compile()
      delete process.env.S3_FILE_ACESS_KEY
      await expect(compile).rejects.toThrow()
    })
  })
})
