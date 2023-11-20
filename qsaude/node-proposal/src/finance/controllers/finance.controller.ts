import { Body, Controller, Get, Param, Patch, Post } from '@nestjs/common'
import { FINANCE_CONTROLLER } from '../constants'
import { CreateFinanceUuidDto, GetFinanceByProposalParamsDto, GetFinanceParamsDto, UpdateFinanceDTO } from '../dtos'
import { AbstractFinanceService } from '../services/abstract/finance.service.abstract'

@Controller(FINANCE_CONTROLLER)
export class FinanceController {
  constructor(private readonly financeService: AbstractFinanceService) {}

  @Get(':idProposal')
  get(@Param() { idProposal }: GetFinanceByProposalParamsDto) {
    return this.financeService.findOneBy({ idProposal })
  }

  @Post()
  create(@Body() createFinanceDto: CreateFinanceUuidDto) {
    return this.financeService.create(createFinanceDto)
  }

  @Patch(':idFinance')
  update(@Param() { idFinance }: GetFinanceParamsDto, @Body() updateFinanceDto: UpdateFinanceDTO) {
    return this.financeService.update(idFinance, updateFinanceDto)
  }
}
