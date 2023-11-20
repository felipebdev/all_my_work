import { Finance, FinanceInput, UpdateFinanceInput } from '@app/finance/models'
import { HttpService } from '@nestjs/axios'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class FinanceService {
  msBaseUrl: string

  constructor(private readonly httpService: HttpService, private readonly configService: ConfigService) {
    this.msBaseUrl = this.configService.get<string>('ms.proposal')
  }

  async getByProposal(idProposal: string): Promise<Finance> {
    try {
      const { msBaseUrl } = this
      const { data } = await this.httpService.axiosRef.get(msBaseUrl.concat(`/finance/${idProposal}`))
      return data as Finance
    } catch (error) {
      if (error.response?.status === 404) {
        throw new NotFoundException(error.response?.data?.message)
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async create(finance: FinanceInput): Promise<Finance> {
    try {
      const { msBaseUrl } = this
      const { data } = await this.httpService.axiosRef.post(msBaseUrl.concat(`/finance`), finance)
      return data as Finance
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async update(idFinance: string, finance: UpdateFinanceInput): Promise<Finance> {
    try {
      const { msBaseUrl } = this
      const { data } = await this.httpService.axiosRef.patch(msBaseUrl.concat(`/finance/${idFinance}`), finance)
      return data as Finance
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }
}
