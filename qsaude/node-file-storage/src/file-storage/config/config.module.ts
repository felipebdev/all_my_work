import MainConfigs from '@app/file-storage/config/main/main.config'
import ApplicationsConfigs from '@app/file-storage/config/applications/applications.config'
import { Module } from '@nestjs/common'
import { ConfigModule as NestConfigModule } from '@nestjs/config'

@Module({
  imports: [
    NestConfigModule.forRoot({
      ignoreEnvFile: process.env.NODE_ENV === 'production',
      envFilePath: process.env.NODE_ENV === 'test' ? 'test.env' : '.env',
      expandVariables: process.env.NODE_ENV !== 'production',
      cache: process.env.NODE_ENV === 'production',
      isGlobal: true
    }),
    NestConfigModule.forFeature(MainConfigs),
    NestConfigModule.forFeature(ApplicationsConfigs)
  ],
  controllers: [],
  providers: []
})
export class ConfigModule {}
