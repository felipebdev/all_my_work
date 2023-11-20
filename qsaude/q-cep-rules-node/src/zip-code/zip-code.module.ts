import { Module } from '@nestjs/common'
import { AbstractZipCodeService, ZipCodeService } from '@app/zip-code/services'
import { ZipCodeController } from '@app/zip-code/controllers'
import { TypeOrmModule } from '@nestjs/typeorm'
import { ZipCodeEntity } from '@app/zip-code/entities'
import { ConfigModule } from '@nestjs/config'
import { externalMSsConfig } from '@app/zip-code/configs'

@Module({
  imports: [TypeOrmModule.forFeature([ZipCodeEntity]), ConfigModule.forFeature(externalMSsConfig())],
  providers: [{ provide: AbstractZipCodeService, useClass: ZipCodeService }],
  controllers: [ZipCodeController]
})
export class ZipCodeModule {}
