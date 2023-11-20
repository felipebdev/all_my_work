import { Injectable, InternalServerErrorException } from '@nestjs/common'
import { HttpService } from '@nestjs/axios'
import { ConfigService } from '@nestjs/config'
import { CreateTokenInput } from '@app/token/models/token.input.model'
import { Token } from '@app/token/models/token.model'
import { CheckTokenInput } from '../models/token.input.model'

@Injectable()
export class TokenService {
  msBaseUrl: string
  constructor(private httpService: HttpService, private readonly configService: ConfigService) {
    this.msBaseUrl = this.configService.get<string>('ms.token')
  }

  async create(tokenInput: CreateTokenInput): Promise<Token> {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.post(msBaseUrl.concat('/token'), tokenInput)
      return response.data as Token
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }

  async check(checkTokenInput: CheckTokenInput): Promise<boolean> {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.post(msBaseUrl.concat('/token/check'), checkTokenInput)
      return response.data as boolean
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }
}
