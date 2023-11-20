import { Module } from '@nestjs/common'
import { JwtModule } from '@nestjs/jwt'
import { PassportModule } from '@nestjs/passport'
import { JwtStrategy } from '@app/auth/strategies'
import { ConfigModule } from '@nestjs/config'
import { authConfig } from './config/auth.config'

@Module({
  imports: [
    PassportModule.register({ defaultStrategy: 'jwt' }),
    JwtModule.register({
      secret: process.env.JWT_SECRET,
      global: false
    }),
    ConfigModule.forFeature(authConfig())
  ],
  providers: [JwtStrategy],
  exports: [PassportModule, JwtStrategy]
})
export class AuthModule {}
