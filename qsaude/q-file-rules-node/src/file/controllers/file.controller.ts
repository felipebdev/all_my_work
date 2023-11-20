import { Controller, Get, Post, Body, Param, Query, StreamableFile, Res } from '@nestjs/common'
import { FileService } from '@app/file/services/file.service'
import { CreateFileDto } from '../dto/create-file.dto'
import { UseInterceptors } from '@nestjs/common'
import { UploadedFile } from '@nestjs/common'
import { Express } from 'express'
import { UsePipes } from '@nestjs/common'
import { FileValidationPipe } from '../pipes/file-validation.pipe'
import { FileInterceptor } from '@nestjs/platform-express'
import { Delete } from '@nestjs/common'

@Controller('file')
export class FileController {
  constructor(private readonly fileService: FileService) {}

  @Post()
  @UsePipes(FileValidationPipe)
  @UseInterceptors(FileInterceptor('file'))
  create(@UploadedFile() file: Express.MulterS3.File, @Body() createFileDto: CreateFileDto) {
    return this.fileService.create(createFileDto, file)
  }

  @Get()
  findAllByProposalAndPersonAndOrigin(
    @Query('origin') origin: string,
    @Query('idProposal') idProposal: string,
    @Query('idPerson') idPerson: string,
    @Query('idCompany') idCompany: string
  ) {
    return this.fileService.findAllByProposalAndPersonAndOrigin(origin, idProposal, idPerson, idCompany)
  }

  @Get(':id')
  findOne(@Param('id') id: string) {
    return this.fileService.findOne(id)
  }

  @Get('preview/:id')
  async previewFile(@Param('id') id: string) {
    const file = await this.fileService.preview(id)
    return new StreamableFile(file.Body as Uint8Array, {
      type: file.ContentType,
      length: file.ContentLength
    })
  }

  @Delete(':id')
  delete(@Param('id') id: string) {
    return this.fileService.delete(id)
  }
}
