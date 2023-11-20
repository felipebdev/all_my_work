import { BigIDRoutes, BigIDValidatedDocument } from '@app/store-identity/interfaces'
import { AbstractBigIdService } from '@app/store-identity/services/abstract'
import { HttpService } from '@nestjs/axios'
import { Injectable } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class BigIDService implements AbstractBigIdService {
  private readonly BASE_URL: string
  private readonly ACCESS_TOKEN: string

  constructor(private readonly configService: ConfigService, private readonly httpService: HttpService) {
    this.BASE_URL = this.configService.get<string>('external-services.bigId.baseUrl')
    this.ACCESS_TOKEN = this.configService.get<string>('external-services.bigId.accessToken')
  }

  async ocr(base64File: string): Promise<string> {
    const URL = this.BASE_URL.concat(BigIDRoutes.OCR)

    const headers = {
      Authorization: 'Bearer '.concat(this.ACCESS_TOKEN),
      'Content-Type': 'application/json'
    }

    const {
      data: { ...validation }
    }: { data: BigIDValidatedDocument } = await this.httpService.axiosRef.post(
      URL,
      {
        Parameters: ['DOC_IMG='.concat(base64File)]
      },
      { headers }
    )

    if (!validation.DocInfo.DOCTYPE || !validation.DocInfo.CPF) return null

    const document = validation.DocInfo.CPF.replace(/[^\w\s]/gi, '')

    return document
  }
}
