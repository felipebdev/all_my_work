import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'
import { HttpService } from '@nestjs/axios'
import { Parametrization } from '@app/parametrization/model/parametrization.model'
import { ParametrizationArgs } from '@app/parametrization/model/parametrization.input.model'

@Injectable()
export class ParametrizationService {
  msBaseUrl: string

  constructor(private readonly configService: ConfigService, private readonly httpService: HttpService) {
    this.msBaseUrl = this.configService.get<string>('ms.parametrization')
  }

  async getParametrization({ beneficiaryType, saleType }: ParametrizationArgs): Promise<Parametrization[]> {
    try {      
      const responseParametrization = await this.httpService.axiosRef.get(this.msBaseUrl.concat(`/parametrization/document?beneficiaryType=${beneficiaryType}&saleType=${saleType}`))
      const responsedParametrization = responseParametrization.data as Parametrization[]
      
      return responsedParametrization
    } catch (error) {
      if (error.response?.status === 404) {
        throw new NotFoundException(error.response?.data?.message)
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }
}
