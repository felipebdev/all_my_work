import { AddressArgs, CompanyAddressInput, PersonAddressInput } from '@app/address/models/address.input.model'
import { Address } from '@app/address/models/address.model'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'
import { HttpService } from '@nestjs/axios'

@Injectable()
export class AddressService {
  msBaseUrl: string

  constructor(private readonly configService: ConfigService, private readonly httpService: HttpService) {
    this.msBaseUrl = this.configService.get<string>('ms.proposal')
  }

  async getAddressByPersonId({ idProposal, role }: AddressArgs): Promise<Address> {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.get(
        msBaseUrl.concat(`/address/person/${idProposal}?role=${role}`)
      )
      return response.data as Address
    } catch (error) {
      if (error.response?.status === 404) {
        throw new NotFoundException(error.response?.data?.message)
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async createPersonAddress(addressInput: PersonAddressInput): Promise<Address> {
    try {
      const { msBaseUrl } = this
      const { idProposal, role, ...addressInputDto } = addressInput
      const response = await this.httpService.axiosRef.post(
        msBaseUrl.concat(`/address/person/${idProposal}?role=${role}`),
        addressInputDto
      )
      return response.data as Address
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async createCompanyAddress(addressInput: CompanyAddressInput) {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.post(msBaseUrl.concat(`/address/company`), addressInput)
      return response.data as Address
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }
}
