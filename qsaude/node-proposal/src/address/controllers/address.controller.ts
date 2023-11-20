import { Body, Controller, Get, Param, Post, Patch, Query } from '@nestjs/common'
import { ADDRESS_CONTROLLER } from '@app/address/constants'
import { AbstractAddressService } from '../services/abstract/address.service.abstract'
import { CreatePersonAddressDto, CreateCompanyAddressDto, UpdateAddressDto } from '../dtos/address.dto'
import { ProposalRole } from '@app/proposal/interfaces'

@Controller(ADDRESS_CONTROLLER)
export class AddressController {
  constructor(private readonly addressService: AbstractAddressService) {}

  @Get(':id')
  async findOne(@Param('id') id: string) {
    return this.addressService.findOne(id)
  }

  @Get('/person/:idProposal')
  async findOneByPerson(
    @Param('idProposal')
    idProposal: string,
    @Query('role')
    role: ProposalRole
  ) {
    return this.addressService.findOneByProposalRole(idProposal, role)
  }

  @Post('/person/:idProposal')
  async createPersonAddress(
    @Param('idProposal')
    idProposal: string,
    @Query('role')
    role: ProposalRole,
    @Body()
    addressDto: CreatePersonAddressDto
  ) {
    return this.addressService.createPersonAddress(idProposal, role, addressDto)
  }

  @Post('/company')
  async createCompanyAddress(@Body() addressDto: CreateCompanyAddressDto) {
    return this.addressService.createCompanyAddress(addressDto)
  }

  @Get('/company/:id')
  async getCompanyAddress(@Param('id') idCompany: string) {
    return this.addressService.findOneByCompany(idCompany)
  }

  @Patch(':id')
  async patch(@Param('id') id: string, @Body() updateAddressDto: UpdateAddressDto) {
    return this.addressService.update(id, updateAddressDto)
  }
}
