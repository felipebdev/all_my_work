import { DynamicModule, Module, Global } from '@nestjs/common'
import { WinstonModule } from 'nest-winston'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { appConfig, databaseConfig, googleStorageConfig } from '@app/common/config'
import { GCPStorageService, HealthService } from '@app/common/services'
import { HealthController } from '@app/common/controllers'
import { format, transports } from 'winston'
import { LogLevelCode, customLevels } from '@app/common/constants'
import { CommonModuleOptions } from '@app/common/interfaces'
import { HttpModule } from '@nestjs/axios'
import { TerminusModule } from '@nestjs/terminus'
import { ApmModule } from 'nestjs-elastic-apm'
import { ContextService } from '@app/common/services'
import { CacheModule } from '@nestjs/cache-manager'
import { TypeOrmModule, TypeOrmModuleOptions } from '@nestjs/typeorm'
import { AttemptEntity, AttemptsStepsEntity, BankCredentialsEntity, CredentialsEntity } from '@app/common/entities'
import { CorrelationIdMiddleware } from '@app/common/middlewares'
import {
  AttemtpsRepository,
  AttemtpsStepsRepository,
  BankCredentialsRepository,
  CredentialsRepository
} from '@app/common/repositories'

@Global()
@Module({})
export class CommonModule {
  static register(options: CommonModuleOptions): DynamicModule {
    return {
      module: CommonModule,
      imports: [
        ApmModule.register(),

        // App Configs
        ConfigModule.forRoot({ ...options.configModule }),
        ConfigModule.forFeature(appConfig()),
        ConfigModule.forFeature(databaseConfig()),
        ConfigModule.forFeature(googleStorageConfig()),

        // Logger Configuration
        WinstonModule.forRoot({
          levels: customLevels,
          transports: [
            new transports.Console({
              level: 'debug',
              format: format.printf((info) => {
                return `${JSON.stringify({
                  ...info,
                  level: LogLevelCode[info.level.toLocaleUpperCase()]
                })}`
              })
            })
          ]
        }),

        // Cache, Axios and HealthCheck configuration
        HttpModule,
        TerminusModule,
        CacheModule.register(),

        // Database configuration
        TypeOrmModule.forRootAsync({
          inject: [ConfigService],
          useFactory: async (configService: ConfigService): Promise<TypeOrmModuleOptions> => {
            return {
              type: configService.get<'mysql'>('database.type'),
              host: configService.get<string>('database.host'),
              port: configService.get<number>('database.port'),
              username: configService.get<string>('database.username'),
              password: configService.get<string>('database.password'),
              database: configService.get<string>('database.name'),
              synchronize: true,
              entities: [],
              autoLoadEntities: true
            }
          }
        }),
        TypeOrmModule.forFeature([CredentialsEntity, AttemptEntity, BankCredentialsEntity, AttemptsStepsEntity])
      ],
      providers: [
        HealthService,
        CorrelationIdMiddleware,
        ContextService,
        GCPStorageService,
        AttemtpsStepsRepository,
        AttemtpsRepository,
        BankCredentialsRepository,
        CredentialsRepository
      ],
      controllers: [HealthController],
      exports: [
        HttpModule,
        GCPStorageService,
        ContextService,
        AttemtpsStepsRepository,
        AttemtpsRepository,
        BankCredentialsRepository,
        CredentialsRepository
      ]
    }
  }
}
