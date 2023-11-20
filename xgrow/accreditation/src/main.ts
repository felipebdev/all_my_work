import * as dotenv from 'dotenv'
dotenv.config()
// eslint-disable-next-line @typescript-eslint/no-unused-vars
// import apm from 'nestjs-elastic-apm'
import { NestFactory } from '@nestjs/core'
import { MainModule } from '@app/main.module'
import { ValidationPipe, VersioningType } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'
import { DocumentBuilder, SwaggerModule } from '@nestjs/swagger'
import { WINSTON_MODULE_NEST_PROVIDER } from 'nest-winston'
import helmet from 'helmet'
import { CorrelationIdMiddleware } from '@app/common/middlewares'

async function bootstrap() {
  const app = await NestFactory.create(MainModule, { bufferLogs: true })
  app.use(app.get(CorrelationIdMiddleware).use)
  app.useLogger(app.get(WINSTON_MODULE_NEST_PROVIDER))
  app.useGlobalPipes(
    new ValidationPipe({
      validationError: { target: true, value: true },
      whitelist: true,
      forbidUnknownValues: true,
      transform: true,
      forbidNonWhitelisted: true
    })
  )
  app.enableVersioning({
    type: VersioningType.URI
  })
  const configService = app.get(ConfigService)
  const config = new DocumentBuilder()
    .setTitle(configService.get<string>('app.name'))
    .setDescription(configService.get<string>('app.description'))
    .setVersion(configService.get<string>('app.version'))
    .build()
  const document = SwaggerModule.createDocument(app, config)
  SwaggerModule.setup('docs', app, document)
  app.use(helmet())
  app.enableCors()
  app.enableShutdownHooks()
  await app.listen(configService.get<string>('app.port'))
}

bootstrap()
  .then(() => true)
  .catch((err) => console.error(err))
