import { AbstractSerproService } from '@app/store-identity/services/abstract'
import { HttpService } from '@nestjs/axios'
import { Injectable, UnprocessableEntityException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class SerproService implements AbstractSerproService {
  private readonly BASE_URL: string
  private readonly ACCOUNT_KEY: string
  private readonly SECRET_KEY: string

  constructor(private readonly configService: ConfigService, private readonly httpService: HttpService) {
    this.BASE_URL = this.configService.get<string>('external-services.serpro.baseUrl')
    this.ACCOUNT_KEY = this.configService.get<string>('external-services.serpro.accountKey')
    this.SECRET_KEY = this.configService.get<string>('external-services.serpro.secretKey')
  }

  private async authenticate(): Promise<string> {
    const { BASE_URL, ACCOUNT_KEY: username, SECRET_KEY: password } = this

    const URL = BASE_URL.concat('/token?grant_type=client_credentials')

    const { data } = await this.httpService.axiosRef.post(URL, {}, { auth: { username, password } })

    if (!data.access_token) throw new UnprocessableEntityException('Erro ao obter token Serpro')

    return data.access_token
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  private async getCNPJInfo(cnpj: string): Promise<any> {
    const token = await this.authenticate()

    const { BASE_URL } = this

    const URL = BASE_URL.concat(`/consulta-cnpj-df/v2/qsa/${cnpj}`)

    const headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      Authorization: `Bearer ${token}`
    }

    try {
      const { data } = await this.httpService.axiosRef.get(URL, { headers })
      return data
    } catch (error) {
      return null
    }
  }

  private getLastName(name: string): string {
    name = name.replace(
      /àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ/g,
      'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
    )

    const temp = name.split(' ')

    return temp[temp.length - 1].toUpperCase()
  }

  public async validateRelationsSerpro(cnpj: string, cpf: string, ownerName: string | null = null): Promise<boolean> {
    const serproQueue = await this.getCNPJInfo(cnpj)

    if (!serproQueue) {
      return false
    }

    if (serproQueue['socios']) {
      for (const socio of serproQueue['socios']) {
        if (socio.cpf && socio.cpf === cpf) {
          return true
        } else if (socio.cnpj) {
          const anotherResult = await this.getCNPJInfo(cnpj)
          for (const anotherSocio of anotherResult['socios']) {
            if (anotherSocio.cpf && anotherSocio.cpf === cpf) {
              return true
            }
          }
        }
      }
      return false
    } else {
      // If it is MEI
      const nameCPF = serproQueue['nomeEmpresarial'].replace(/[^0-9]/g, '')

      if (nameCPF === cpf) {
        return true
      }

      // If it is an individual company, it is not mandatory to declare the partner,
      // but there is a rule that the last name of the owner must be the last of the company name.
      const bussinessLastName = this.getLastName(serproQueue['nomeEmpresarial'])
      const userLastName = this.getLastName(ownerName)

      if (bussinessLastName === userLastName) {
        // Log when there is no economic relationship
        return true
      }
      return false
    }
  }
}
