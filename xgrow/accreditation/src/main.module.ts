import { Module } from '@nestjs/common'
import { CommonModule } from '@app/common/common.module'
import { AuthModule } from '@app/auth/auth.module'
import { StoreIdentityModule } from '@app/store-identity/store-identity.module'

@Module({
  imports: [
    CommonModule.register({
      configModule: {
        ignoreEnvFile: false,
        envFilePath: '.env',
        cache: ['production', 'staging'].includes(process.env.NODE_ENV),
        isGlobal: true
      }
    }),
    AuthModule,
    StoreIdentityModule
  ]
})
export class MainModule {}
