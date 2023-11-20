import { Module } from '@nestjs/common';
import { AuthService } from '@app/auth/services';
import { UsersModule } from '@app/users/users.module';
import { PassportModule } from '@nestjs/passport';
import { LocalStrategy } from '@app/auth/strategies';
import { JwtModule } from '@nestjs/jwt';
import { AuthController } from './controllers/auth.controller';

@Module({
  imports: [
    UsersModule,
    PassportModule,
    JwtModule.register({
      secret: 'ANY_SECRET_THAT_SHOULD_CHANGE',
      signOptions: { expiresIn: '100000s' },
    }),
  ],
  providers: [AuthService, LocalStrategy],
  controllers: [AuthController],
})
export class AuthModule {}
