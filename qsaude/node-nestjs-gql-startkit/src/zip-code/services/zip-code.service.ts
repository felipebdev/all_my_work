import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'
import { HttpService } from '@nestjs/axios'
import { Address } from '@app/address/models/address.model'

@Injectable()
export class ZipCodeService {
  msBaseUrl: string

  constructor(private readonly configService: ConfigService, private readonly httpService: HttpService) {
    this.msBaseUrl = this.configService.get<string>('ms.zipCode')
  }

  async getAddress(zipCodeInput: string): Promise<Address> {
    try {
      const { msBaseUrl } = this
      const addressResponse = await this.httpService.axiosRef.get(msBaseUrl.concat(`/cep/${zipCodeInput}`))
      const {
        data: { zipCode, address, district: neighborhood, state, city }
      } = addressResponse
      return {
        address,
        city,
        neighborhood,
        state,
        zipCode
      }
    } catch (error) {
      if (error.response?.status === 404) {
        throw new NotFoundException(error.response?.data?.message)
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }
}
