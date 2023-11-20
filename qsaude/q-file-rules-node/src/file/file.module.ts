import { Module } from '@nestjs/common'
import { FileService } from './services/file.service'
import { FileController } from './controllers/file.controller'
import { DynamooseModule } from 'nestjs-dynamoose'
import { FileEntity } from './entities/file.entity'
import { MulterModule } from '@nestjs/platform-express'
import { ConfigModule } from '@nestjs/config'
import { s3Config } from '@app/file/configs'

@Module({
  imports: [
    ConfigModule.forFeature(s3Config()),
    DynamooseModule.forFeature([{ name: 'q-ecomm-files', schema: FileEntity }]),
    MulterModule
  ],
  controllers: [FileController],
  providers: [FileService]
})
export class FileModule {}
