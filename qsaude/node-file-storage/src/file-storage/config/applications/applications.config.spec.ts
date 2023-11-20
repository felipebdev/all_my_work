import { ConfigModule } from '@app/file-storage/config/config.module'
import ApplicationsConfigs from '@app/file-storage/config/applications/applications.config'
import { IApplicationsSettings } from '@app/file-storage/shared/interfaces/applications-settings.interface'
import { ConfigModule as NestConfigModule, ConfigService } from '@nestjs/config'
import { Test } from '@nestjs/testing'

describe('ApplicationsConfigs', () => {
  let configService: ConfigService
  it('should have the appropriate settings', async () => {
    const moduleRef = await Test.createTestingModule({
      imports: [ConfigModule, NestConfigModule.forFeature(ApplicationsConfigs)],
      controllers: [],
      providers: []
    }).compile()

    configService = moduleRef.get<ConfigService>(ConfigService)
    const configs = configService.get<IApplicationsSettings>('applications')
    expect(configs.portalEmpresas).toHaveProperty('aws')
    expect(configs.portalEmpresas.aws).toHaveProperty('s3')
    expect(configs.portalEmpresas.aws.s3).toHaveProperty('bucket')
    expect(configs.portalEmpresas).toHaveProperty('storage')
    expect(configs.portalEmpresas.storage).toHaveProperty('path')
    expect(configs.portalEmpresas).toHaveProperty('notification')
    expect(configs.portalEmpresas.notification).toHaveProperty('importCsvTransactions')
  })
  it('should throw a error if appropriate setting is not present', async () => {
    delete process.env.PORTAL_EMPRESAS_BUCKET
    const compile = Test.createTestingModule({
      imports: [ConfigModule, NestConfigModule.forFeature(ApplicationsConfigs)],
      controllers: [],
      providers: []
    }).compile()
    await expect(compile).rejects.toThrow()
  })
})
