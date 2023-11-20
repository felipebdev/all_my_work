import { CorrelationIdMiddleware } from '@app/common/middlewares'
import { MainModule } from '@app/main.module'
import { ClassSerializerInterceptor, ValidationPipe, VersioningType } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'
import { NestFactory, Reflector } from '@nestjs/core'
import { Logger, LoggerErrorInterceptor } from 'nestjs-pino'

async function bootstrap() {
  const app = await NestFactory.create(MainModule, { bufferLogs: true })
  app.use(app.get(CorrelationIdMiddleware).use)
  app.useLogger(app.get(Logger))
  app.useGlobalInterceptors(new LoggerErrorInterceptor())
  app.useGlobalInterceptors(new ClassSerializerInterceptor(app.get(Reflector)))
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
  app.enableCors()
  app.enableShutdownHooks()
  await app.listen(configService.get<number>('app.port') ?? 3000)
}
bootstrap()
  .then()
  .catch((err) => console.error(err))
