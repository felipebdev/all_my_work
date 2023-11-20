import { Module } from '@nestjs/common'
import { CommonModule } from './common/common.module'
import { FileStorageModule } from '@app/file-storage/storage.module'

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
    FileStorageModule
  ]
})
export class MainModule {}
