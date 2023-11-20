import { PersonInput } from '@app/person/models/person.input.model'
import { Person } from '@app/person/models/person.model'
import { HttpService } from '@nestjs/axios'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class PersonService {
  msBaseUrl: string

  constructor(private readonly httpService: HttpService, private readonly configService: ConfigService) {
    this.msBaseUrl = this.configService.get<string>('ms.proposal')
  }

  async getPersonById(id: string): Promise<Person> {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.get(msBaseUrl.concat(`/person/${id}`))
      return response.data as Person
    } catch (error) {
      if (error.response?.status === 404) {
        throw new NotFoundException(error.response?.data?.message)
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async create(personInput: PersonInput): Promise<Person> {
    try {
      const { msBaseUrl } = this
      const response = await this.httpService.axiosRef.post(msBaseUrl.concat('/person'), personInput)
      return response.data as Person
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }

  async createPersonContacts(contactsDto: { refId: string; email: string; cellphone: string }): Promise<void> {
    try {
      const { msBaseUrl } = this
      await this.httpService.axiosRef.post(msBaseUrl.concat('/contact/person/proposal-role'), contactsDto)
    } catch (error) {
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }
}
