import { HttpService } from '@nestjs/axios'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class LeadService {
  msBaseUrl: string

  constructor(private readonly httpService: HttpService, private readonly configService: ConfigService) {
    this.msBaseUrl = this.configService.get<string>('ms.lead')
  }

  async getLeadById(id: string) {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.get(msBaseUrl.concat(`/lead/${id}`))
      return response.data
    } catch (error) {
      if (error.response?.status === 404) {
        throw new NotFoundException(error.response?.data?.message)
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }
}
