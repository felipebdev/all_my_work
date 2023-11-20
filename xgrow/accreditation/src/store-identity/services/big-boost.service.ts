import { Injectable, UnprocessableEntityException } from '@nestjs/common'
import { HttpService } from '@nestjs/axios'
import { ConfigService } from '@nestjs/config'
import {
  BigBoostAuthenticateResponse,
  BigBoostRoutes,
  BigBoostAuthenticateBody,
  BigBoostExceptions,
  BigBoostCompaniesSearchBody,
  BigBoostRelationshipsResponse,
  BigBoostBasicDataResponse
} from '@app/store-identity/interfaces'
import { AbstractBigBoostService } from '@app/store-identity/services/abstract'

@Injectable()
export class BigBoostService implements AbstractBigBoostService {
  private readonly BASE_URL: string
  private readonly USER: string
  private readonly PASSWORD: string
  private readonly AUTHENTICATE_EXPIRICY: number
  private readonly DEFAULT_HEADERS: object

  constructor(private readonly configService: ConfigService, private readonly httpService: HttpService) {
    this.BASE_URL = this.configService.get<string>('external-services.bigBoost.baseUrl')
    this.USER = this.configService.get<string>('external-services.bigBoost.user')
    this.PASSWORD = this.configService.get<string>('external-services.bigBoost.password')
    this.DEFAULT_HEADERS = { 'Content-Type': 'application/json' }
    this.AUTHENTICATE_EXPIRICY = 8750
  }

  private async authenticate(): Promise<string> {
    const { USER: login, PASSWORD: password, AUTHENTICATE_EXPIRICY: expires, DEFAULT_HEADERS: headers } = this

    const URL = this.BASE_URL.concat(BigBoostRoutes.AUTHENTICATE)

    const body: BigBoostAuthenticateBody = {
      login,
      password,
      expires
    }

    const {
      data: { token, success }
    }: { data: BigBoostAuthenticateResponse } = await this.httpService.axiosRef.post(URL, body, { headers })

    if (!success) throw new UnprocessableEntityException(BigBoostExceptions.AUTHENTICATE_ERROR)

    return token
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private async cnpjRelationships(cnpj: string): Promise<Array<any>> {
    const AccessToken = await this.authenticate()

    const URL = this.BASE_URL.concat(BigBoostRoutes.COMPANIES)

    const { DEFAULT_HEADERS: headers } = this

    const body: BigBoostCompaniesSearchBody = {
      Datasets: 'relationships',
      q: `doc{${cnpj}}`,
      AccessToken
    }

    const { data }: { data: BigBoostRelationshipsResponse } = await this.httpService.axiosRef.post(URL, body, {
      headers
    })

    const ownership = data.Result[0].Relationships.Relationships || []

    const filteredOwnership = ownership.filter((array) => {
      if (array.RelationshipType.toUpperCase() !== 'EMPLOYEE') {
        return array
      }
    })

    return filteredOwnership
  }

  public async personalData(cpf: string): Promise<object> {
    const AccessToken = await this.authenticate()

    const URL = this.BASE_URL.concat(BigBoostRoutes.PEOPLE)

    const { DEFAULT_HEADERS: headers } = this

    const body: BigBoostCompaniesSearchBody = {
      Datasets: 'basic_data',
      q: `doc{${cpf}}`,
      AccessToken
    }

    const { data }: { data: BigBoostBasicDataResponse } = await this.httpService.axiosRef.post(URL, body, { headers })

    return data?.Result[0]?.BasicData
  }

  public async validateRelationship(cnpj: string, cpf: string): Promise<boolean> {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const relationships: Array<any> = await this.cnpjRelationships(cnpj)

    const hasRelationship = relationships.find(
      (relationship) => relationship['RelatedEntityTaxIdNumber'] === cpf.replace(/[^0-9]/g, '')
    )

    if (hasRelationship) return true

    for (const relationship of relationships) {
      if (relationship['RelatedEntityTaxIdNumber'].length > 11) {
        const anotherRelationships = await this.cnpjRelationships(relationship['RelatedEntityTaxIdNumber'])

        for (const anotherRelationship of anotherRelationships) {
          if (anotherRelationship['RelatedEntityTaxIdNumber'] === cpf.replace(/[^0-9]/g, '')) {
            return true
          }
        }
      }
    }
  }
}
