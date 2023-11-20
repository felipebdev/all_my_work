import { DynamicModule, Module } from '@nestjs/common'
import {SqsModule as NestSqsModule } from '@nestjs-packages/sqs'
import { SqsPoller } from '@app/sqs/services/sqs.poller'
import { SqsController } from '@app/sqs/controllers/sqs.controller'
import { ISqsModuleConfig } from '@app/sqs/interfaces'
import { ConfigModule } from '@nestjs/config'
import { sqsConfig } from '@app/sqs/config'
import { AbstractSqsPoller } from '@app/sqs/services/abstract'

@Module({})
export class SqsModule {
  public static async registerAsync({config, queues}: ISqsModuleConfig): Promise<DynamicModule> {
    return {
      module: SqsModule,
      imports: [
        ConfigModule.forFeature(sqsConfig()),
        NestSqsModule.forRootAsync(config),
        NestSqsModule.registerQueue(...queues),
      ],
      providers: [
        {
          provide: AbstractSqsPoller,
          useClass: SqsPoller
        },
      ],
      controllers: [SqsController]
    }
  }

}
