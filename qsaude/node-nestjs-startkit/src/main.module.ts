import { CommonModule } from '@app/common/common.module'
import { SqsConfig, SqsConfigOption, SqsQueueType } from '@nestjs-packages/sqs'
import { Module } from '@nestjs/common'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { AuthModule } from './auth/auth.module'
import { SqsModule } from '@app/sqs/sqs.module'

@Module({
  imports: [
    CommonModule.register({
      configModule: {
        ignoreEnvFile: ['production', 'staging'].includes(process.env.NODE_ENV),
        envFilePath: '.env',
        expandVariables: ['development', 'test'].includes(process.env.NODE_ENV),
        cache: ['production', 'staging'].includes(process.env.NODE_ENV),
        isGlobal: true
      }
    }),
    SqsModule.registerAsync({
      config: {
        imports: [ConfigModule],
        useFactory: (configService: ConfigService) => {
          const config: SqsConfigOption = {
            accountNumber: configService.get<string>('sqs.accountNumber'),
            region: configService.get<string>('sqs.region'),
            endpoint: configService.get<string>('sqs.endpoint'),
            credentials:{
              accessKeyId: configService.get<string>('sqs.credentials.accessKeyId'),
              secretAccessKey: configService.get<string>('sqs.credentials.secretAccessKey'),
            },
          }
          return new SqsConfig(config)
        },
        inject: [ConfigService]
      },
      queues:[ 
        {
          name: 'sample-queue',
          type: SqsQueueType.All,
          consumerOptions: {
            waitTimeSeconds: Number(process.env.AWS_SQS_POLLING_TIME)
          },
          producerOptions: {}
        }
      ]
    }),
    AuthModule
  ],
  controllers: [],
  providers: []
})
export class MainModule {}
