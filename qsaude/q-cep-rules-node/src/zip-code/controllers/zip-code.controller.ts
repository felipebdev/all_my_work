import { ZIP_CODE_CONTROLLER } from '@app/zip-code/constants'
import { ZipCodeParamsDto } from '@app/zip-code/dtos'
import { AbstractZipCodeService } from '@app/zip-code/services'
import { Controller, Get, Param } from '@nestjs/common'

@Controller(ZIP_CODE_CONTROLLER)
export class ZipCodeController {
  constructor(private readonly zipCodeService: AbstractZipCodeService) {}

  @Get(':zipCode')
  async getCep(@Param() { zipCode }: ZipCodeParamsDto) {
    return this.zipCodeService.findOne(zipCode)
  }
}
