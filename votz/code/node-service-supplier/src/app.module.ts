import { Module } from '@nestjs/common';
import { CommonModule } from '@app/common/common.module';
import { AuthModule } from '@app/auth/auth.module';
import { UsersModule } from '@app/users/users.module';

@Module({
  imports: [
    CommonModule.registerAsync({
      configModule: {
        envFilePath: '.env',
        // expandVariables: ['development', 'test'].includes(process.env.NODE_ENV),
        // cache: ['production', 'staging'].includes(process.env.NODE_ENV),
        isGlobal: true,
      },
    }),
    UsersModule,
    // AuthModule,
  ],
  controllers: [],
  providers: [],
})
export class AppModule {}
