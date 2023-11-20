import { NotificationModule } from '@app/file-storage/notification/notification.module'
import { IMainSettings } from '@app/file-storage/shared/interfaces/main-settings.interface'
import { AbstractSettingsProvider } from '@app/file-storage/shared/services/abstract-settings-provider.service'
import { StorageController } from '@app/file-storage/storage/controllers/storage.controller'
import { AbstractStorageDriverService } from '@app/file-storage/storage/drivers/abstract-storage-driver.service'
import { AwsS3StorageDriverService } from '@app/file-storage/storage/drivers/aws/aws-s3-storage-driver.service'
import { AbstractStorageService } from '@app/file-storage/storage/services/abstract-storage.service'
import { StorageSettingsProvider } from '@app/file-storage/storage/services/storage-settings.provider'
import { StorageService } from '@app/file-storage/storage/services/storage.service'
import { S3Client, S3ClientConfig } from '@aws-sdk/client-s3'
import { Module } from '@nestjs/common'
import { ConfigService, ConfigModule as NestConfigModule } from '@nestjs/config'
import { MulterModule } from '@nestjs/platform-express'
import { HttpModule } from '@nestjs/axios'
import MainConfigs from '@app/file-storage/config/main/main.config'
import ApplicationsConfigs from '@app/file-storage/config/applications/applications.config'
import * as path from 'path'
import { AuthModule } from '@app/auth/auth.module'

@Module({
  imports: [
    MulterModule.register({
      dest: path.join(__dirname, `/../data`) // TODO: Add to readme
    }),
    NotificationModule,
    HttpModule,
    NestConfigModule.forFeature(MainConfigs),
    NestConfigModule.forFeature(ApplicationsConfigs),
    AuthModule
  ],
  providers: [
    {
      provide: S3Client,
      inject: [ConfigService],
      useFactory: async (configService: ConfigService) => {
        const configs = configService.get<IMainSettings>('main')
        const awsConfig: S3ClientConfig = {
          region: configs.aws.region
        }
        return new S3Client(awsConfig)
      }
    },
    {
      provide: AbstractStorageDriverService,
      useClass: AwsS3StorageDriverService
    },
    {
      provide: AbstractSettingsProvider,
      useClass: StorageSettingsProvider
    },
    {
      provide: AbstractStorageService,
      useClass: StorageService
    }
  ],
  controllers: [StorageController]
})
export class FileStorageModule {}
