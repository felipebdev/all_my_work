import { CommonModuleOptions } from '@app/common/interfaces';
import { DynamicModule, Global, CacheModule } from '@nestjs/common';
import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { MongooseModule, MongooseModuleFactoryOptions } from '@nestjs/mongoose';
import { dbConfig, cacheConfig } from '@app/common/configs';
import redisStore from 'cache-manager-redis-store';
import { CacheService } from '@app/common/services/cache.service';
import { MailerModule, MailerOptions } from '@nestjs-modules/mailer';
import { PugAdapter } from '@nestjs-modules/mailer/dist/adapters/pug.adapter';
import { MailService } from '@app/common/services/mail.service';
import path from 'path';

@Global()
@Module({})
export class CommonModule {
  public static async registerAsync(
    options: CommonModuleOptions,
  ): Promise<DynamicModule> {
    return {
      module: CommonModule,
      // global: true,
      imports: [
        ConfigModule.forRoot({ ...options.configModule }),
        ConfigModule.forFeature(dbConfig()),
        ConfigModule.forFeature(cacheConfig()),
        MongooseModule.forRootAsync({
          imports: [ConfigModule],
          useFactory: async (
            configService: ConfigService,
          ): Promise<MongooseModuleFactoryOptions> => {
            return {
              uri: configService.get<string>('db.uri'),
            };
          },
          inject: [ConfigService],
        }),
        CacheModule.registerAsync({
          inject: [ConfigService],
          useFactory: async (configService: ConfigService) => {
            if (
              configService.get<string>('cache.store').toLowerCase() === 'redis'
            ) {
              return {
                store: redisStore,
                host: configService.get<string>('cache.host'),
                port: configService.get<string>('cache.port'),
                ttl: configService.get<number>('cache.ttl'),
                password: configService.get<string>('cache.password'),
                prefix: configService.get<string>('cache.prefix'),
              };
            } else {
              return null;
            }
          },
        }),
        MailerModule.forRootAsync({
          imports: [ConfigModule],
          useFactory: (configService: ConfigService): MailerOptions => {
            console.log('__dirname', __dirname);
            console.log('env', process.env.PWD);
            return {
              transport:
                'smtps://felipebdev@gmail.com:jzshohloayjisfhx@smtp.gmail.com',
              defaults: {
                from: 'nest-modules" <modules@nestjs.com>',
              },
              template: {
                dir: process.env.PWD + '/src/users/mail-templates',
                adapter: new PugAdapter(),
                options: {
                  strict: true,
                },
              },
              preview: true,
            };
          },
          inject: [ConfigService],
        }),
      ],
      providers: [CacheService, MailService],
      exports: [CacheService, MailService],
    };
  }
}
