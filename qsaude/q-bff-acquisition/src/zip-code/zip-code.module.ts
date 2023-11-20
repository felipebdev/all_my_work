import { Module } from '@nestjs/common'
import { ZipCodeResolver } from '@app/zip-code/resolvers/zip-code.resolver'
import { ZipCodeService } from '@app/zip-code/services/zip-code.service'

@Module({
  providers: [ZipCodeResolver, ZipCodeService]
})
export class ZipCodeModule {}
