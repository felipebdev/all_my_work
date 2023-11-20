import { ConfigModule } from '@app/file-storage/config/config.module'
import MainConfig from '@app/file-storage/config/main/main.config'
import { IMainSettings } from '@app/file-storage/shared/interfaces/main-settings.interface'
import { ConfigModule as NestConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'

describe('MainConfig', () => {
  let configService: ConfigService
  describe('Main Config', () => {
    it('should have the appropriate settings', async () => {
      const moduleRef = await Test.createTestingModule({
        imports: [ConfigModule, NestConfigModule.forFeature(MainConfig)],
        controllers: [],
        providers: []
      }).compile()

      configService = moduleRef.get<ConfigService>(ConfigService)
      const config = configService.get<IMainSettings>('main')
      expect(config).toHaveProperty('name')
      expect(config).toHaveProperty('description')
      expect(config).toHaveProperty('version')
      expect(config).toHaveProperty('port')
      expect(config).toHaveProperty('aws')
      expect(config.aws).toHaveProperty('region')
      expect(config.aws).toHaveProperty('accountId')
    })
    it('should throw a error if appropriate setting is not present', async () => {
      delete process.env.APP_NAME
      const compile = Test.createTestingModule({
        imports: [ConfigModule, NestConfigModule.forFeature(MainConfig)],
        controllers: [],
        providers: []
      }).compile()
      await expect(compile).rejects.toThrow()
    })
  })
})
