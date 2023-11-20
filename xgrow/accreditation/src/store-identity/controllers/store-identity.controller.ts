import {
  Body,
  Controller,
  Post,
  UploadedFile,
  UseGuards,
  UseInterceptors,
  UsePipes,
  Headers,
  Req,
  UseFilters
} from '@nestjs/common'
import { FileInterceptor } from '@nestjs/platform-express'
import { UserDocumentsDTO } from '@app/store-identity/dto'
import { FileValidationPipe } from '@app/store-identity/pipes'
import { JwtAuthGuard } from '@app/auth/guards/jwt-auth-guard'
import { AbstractStoreIdentityService } from '@app/store-identity/services/abstract'
import { HttpExceptionFilter } from '@app/store-identity/filters'
@Controller('store-identity')
export class StoreIdentityController {
  constructor(private readonly storeIdentityService: AbstractStoreIdentityService) {}

  @Post()
  @UsePipes(FileValidationPipe)
  @UseFilters(HttpExceptionFilter)
  @UseGuards(JwtAuthGuard)
  @UseInterceptors(FileInterceptor('file'))
  async validateDocuments(
    @UploadedFile() file: Express.Multer.File,
    @Body() userDocuments: UserDocumentsDTO,
    @Headers() headers,
    @Req() request
  ) {
    const { user } = request
    const correlationId = headers['x-correlation-id']
    return this.storeIdentityService.validateDocuments(file, userDocuments, correlationId, user)
  }
}
