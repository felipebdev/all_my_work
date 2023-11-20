import { COMPANY_CONTROLLER } from '@app/company/constants'
import { GetCompanyParamsDto } from '@app/company/dtos'
import { AbstractCompanyService } from '@app/company/services'
import { Body, Controller, Get, Param, Patch, Post } from '@nestjs/common'
import { CreateCompanyUuidDto, PatchCompanyParamsDto, UpdateCompanyDto } from '@app/company/dtos'

@Controller(COMPANY_CONTROLLER)
export class CompanyController {
  constructor(private readonly companyService: AbstractCompanyService) {}

  @Get(':idProposal')
  getCompanyByProposal(@Param() { idProposal }: GetCompanyParamsDto) {
    return this.companyService.findOneBy({ idProposal })
  }

  @Post()
  create(@Body() createCompanyDto: CreateCompanyUuidDto) {
    return this.companyService.create(createCompanyDto)
  }

  @Patch(':idCompany')
  update(@Param() { idCompany }: PatchCompanyParamsDto, @Body() updateCompanyDto: UpdateCompanyDto) {
    return this.companyService.update(idCompany, updateCompanyDto)
  }
}
