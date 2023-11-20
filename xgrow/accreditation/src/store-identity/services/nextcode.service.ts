import { NextCodeIDExceptionsMessages, NextCodeValidatedDocument, Routes } from '@app/store-identity/interfaces'
import { AbstractNextcodeService } from '@app/store-identity/services/abstract'
import { HttpService } from '@nestjs/axios'
import { Injectable, UnprocessableEntityException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class NextcodeService implements AbstractNextcodeService {
  private readonly BASE_URL: string
  private readonly ACCESS_TOKEN: string

  constructor(private readonly configService: ConfigService, private readonly httpService: HttpService) {
    this.BASE_URL = this.configService.get<string>('external-services.nextcode.baseUrl')
    this.ACCESS_TOKEN = this.configService.get<string>('external-services.nextcode.accessToken')
  }

  private extractCpfFromOcr(validation: NextCodeValidatedDocument): string {
    const { data } = validation

    const extractedCpf = data[0]?.extraction?.person?.taxId

    const cpfFieldExists = data.length && ![null, undefined, ''].includes(extractedCpf)

    if (!cpfFieldExists)
      throw new UnprocessableEntityException({
        validate_error: true,
        message: NextCodeIDExceptionsMessages.NOCPF
      })

    return extractedCpf.replace(/[^\w\s]/gi, '')
  }

  async ocr(fileName: string, base64File: string): Promise<string> {
    const URL_WITH_PARAMS = this.BASE_URL.concat(Routes.OCR)

    const headers = {
      authorization: 'ApiKey '.concat(this.ACCESS_TOKEN),
      'Content-Type': 'application/json'
    }

    const { data } = await this.httpService.axiosRef.post(
      URL_WITH_PARAMS,
      {
        base64: {
          [fileName]: base64File
        }
      },
      { headers }
    )

    return this.extractCpfFromOcr(data)
  }
}
