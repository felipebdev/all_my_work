import { Company } from '@app/company/models'
import { HttpService } from '@nestjs/axios'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'
import { CompanyInput, UpdateCompanyInput } from '@app/company/models'
import { Address } from '@app/address/models/address.model'

@Injectable()
export class CompanyService {
  msBaseUrl: string

  constructor(private readonly httpService: HttpService, private readonly configService: ConfigService) {
    this.msBaseUrl = this.configService.get<string>('ms.proposal')
  }

  async getByProposal(idProposal: string): Promise<Company> {
    try {
      const { msBaseUrl } = this
      const { data } = await this.httpService.axiosRef.get(msBaseUrl.concat(`/company/${idProposal}`))
      return data as Company
    } catch (error) {
      if (error.response?.status === 404) {
        throw new NotFoundException(error.response?.data?.message)
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async create(company: CompanyInput): Promise<Company> {
    try {
      const { msBaseUrl } = this
      const { data } = await this.httpService.axiosRef.post(msBaseUrl.concat(`/company`), company)
      return data as Company
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async update(idCompany: string, company: UpdateCompanyInput): Promise<Company> {
    try {
      const { msBaseUrl } = this
      const { data } = await this.httpService.axiosRef.patch(msBaseUrl.concat(`/company/${idCompany}`), company)
      return data as Company
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async getCompanyAddress(idCompany: string): Promise<Address> {
    try {
      const { msBaseUrl } = this
      const { data } = await this.httpService.axiosRef.get(msBaseUrl.concat(`/address/company/${idCompany}`))
      return data as Address
    } catch (error) {
      if (error.response?.status === 404) {
        return {} as any
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }
}
