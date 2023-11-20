import { HttpService } from '@nestjs/axios'
import { Injectable } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'
import { JwtService } from '@nestjs/jwt'
import { CheckoutHeaders, CheckoutJwtPayload, CheckoutRoutes } from '@app/store-identity/interfaces'
import { AbstractCheckoutAPIService } from '@app/store-identity/services/abstract'

@Injectable()
export class CheckoutAPIService implements AbstractCheckoutAPIService {
  private readonly BASE_URL: string

  constructor(
    private readonly configService: ConfigService,
    private readonly httpService: HttpService,
    private readonly jwtService: JwtService
  ) {
    this.BASE_URL = this.configService.get<string>('external-services.checkoutApi.baseUrl')
  }

  public async createRecipient(platformId: string, userId: string, correlationId: string) {
    const token = await this.generateToken(platformId, userId)
    const headers = this.generateHeader(token, correlationId)

    const URL = this.BASE_URL.concat(CheckoutRoutes.CREATE_RECIPIENT)

    // try {
    const { data } = await this.httpService.axiosRef.post(URL, {}, { headers })
    // } catch (error) {
    //   console.log('error', error)
    // }

    return true
  }

  private async generateToken(platform_id: string, user_id: string): Promise<string> {
    const payload: CheckoutJwtPayload = {
      platform_id,
      user_id,
      acting_as: 'client'
    }
    console.log('CHECKOUT_JWT_SECRET', process.env.CHECKOUT_JWT_SECRET)
    return this.jwtService.sign(payload, { expiresIn: '1h' })
  }

  private generateHeader(token: string, correlationId: string): CheckoutHeaders {
    return {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      Authorization: `Bearer ${token}`,
      'X-Correlation-Id': correlationId
    }
  }
}
