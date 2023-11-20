import { ValidationPipe } from '@nestjs/common';
import { NestFactory } from '@nestjs/core';
import { AppModule } from './app.module';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      forbidNonWhitelisted: true,
      transform: true,
    }),
  );
  await app.listen(Number(process.env.APP_PORT) ?? 3000);
}
bootstrap()
  .then(() =>
    console.log(`Magic happens on port ${process.env.APP_PORT ?? 3000}`),
  )
  .catch((err) => console.error(err));
