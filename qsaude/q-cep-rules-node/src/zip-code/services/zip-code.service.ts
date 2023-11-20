import { Injectable, NotFoundException } from '@nestjs/common'
import { AbstractZipCodeService } from '@app/zip-code/services'
import { ConfigService } from '@nestjs/config'
import { HttpService } from '@nestjs/axios'
import { InjectRepository } from '@nestjs/typeorm'
import { ZipCodeEntity } from '@app/zip-code/entities'
import { Repository } from 'typeorm'

@Injectable()
export class ZipCodeService implements AbstractZipCodeService {
  constructor(
    @InjectRepository(ZipCodeEntity)
    private readonly zipCodeRepository: Repository<ZipCodeEntity>,
    private readonly configService: ConfigService,
    private readonly httpService: HttpService
  ) {}

  private async createFromViaCep(zipCodeInput: string): Promise<ZipCodeEntity> {
    const viaCepBaseUrl = await this.configService.get<string>('external.viaCep')
    const viaCepResponse = await this.httpService.axiosRef.get(viaCepBaseUrl.concat(`/${zipCodeInput}/json`))
    const {
      data: { cep: zipCode, logradouro: address, bairro: district, uf: state, localidade: city, ibge: ibgeCode, erro }
    } = viaCepResponse
    if (erro || !zipCode) {
      throw new NotFoundException(`Zip code ${zipCodeInput} not found`)
    }
    const createdAddress = await this.zipCodeRepository.create({
      address,
      city,
      district,
      ibgeCode,
      state,
      zipCode: String(zipCode).replace(/[^a-zA-Z0-9 ]/g, '')
    })
    return this.zipCodeRepository.save(createdAddress)
  }

  async findOne(zipCode: string): Promise<ZipCodeEntity> {
    const address = await this.zipCodeRepository.findOneBy({
      zipCode
    })
    if (!address) return this.createFromViaCep(zipCode)

    return address
  }
}
