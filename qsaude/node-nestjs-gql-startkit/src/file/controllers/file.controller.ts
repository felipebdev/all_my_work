import { Controller, Post, Body, UseInterceptors, UploadedFile, Param, Get, Delete } from '@nestjs/common'
import { FileInterceptor } from '@nestjs/platform-express'
import { FileService } from '@app/file/services'
import { CreateFileDto } from '@app/file/dtos'
import { Express } from 'express'
import { UUIDParamsDto } from '@app/shared'

@Controller('file')
export class FileController {
  constructor(private readonly fileService: FileService) {}

  @Post()
  @UseInterceptors(FileInterceptor('file'))
  create(@UploadedFile() file: Express.Multer.File, @Body() createFileDto: CreateFileDto) {
    return this.fileService.create(createFileDto, file)
  }

  @Get('/preview/:id')
  preview(@Param() { id }: UUIDParamsDto) {
    return this.fileService.preview(id)
  }

  @Delete(':id')
  delete(@Param() { id }: UUIDParamsDto) {
    return this.fileService.delete(id)
  }
}
