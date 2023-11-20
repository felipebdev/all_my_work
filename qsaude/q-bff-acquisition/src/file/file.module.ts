import { Module } from '@nestjs/common'
import { FileResolver } from '@app/file/resolvers/file.resolver'
import { FileService } from '@app/file/services/file.service'
import { ParametrizationModule } from '@app/parametrization/parametrization.module'

@Module({
  imports: [ParametrizationModule],
  providers: [FileResolver, FileService]
})
export class FileModule {}
