import { FileArgs } from '@app/file/models/file.input.model'
import { File } from '@app/file/models/file.model'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { ConfigService } from '@nestjs/config'
import { HttpService } from '@nestjs/axios'
import { ParametrizationService } from '@app/parametrization/services/parametrization.service'

@Injectable()
export class FileService {
  msBaseUrl: string
  fileOrigin: string

  constructor(
    private readonly configService: ConfigService, 
    private readonly httpService: HttpService,
    private readonly parametrizationService: ParametrizationService) {
    this.msBaseUrl = this.configService.get<string>('ms.file')
    this.fileOrigin = this.configService.get<string>('ms.fileOrigin')
  }

  async parametrizationFile({ idPerson, idProposal, beneficiaryType, saleType }: FileArgs): Promise<File[]> {
    try {      
      const parametrization = await this.parametrizationService.getParametrization({ beneficiaryType, saleType})
        

      const responseFile = await this.httpService.axiosRef.get(this.msBaseUrl.concat(`/file?origin=${this.fileOrigin}&idProposal=${idProposal}&idPerson=${idPerson}`))      
      const responsedFile = responseFile.data as File[]

      let response: File[] = []
      responsedFile.map((file: File)=> {
        const param =  parametrization.find((p) => p.idFileType === parseInt(file.fileType))

        return response.push({
            id: file.id,
            mandatory: param.mandatory,
            name: param.name,
            fileMimetype: file.fileMimetype,
            fileOriginalname: file.fileOriginalname,
            fileSize: file.fileSize,
            origin: file.origin,
            idProposal: file.idProposal,
            idPerson: file.idPerson,
            fileType: file.fileType
          })
        }
      )

      return response
    } catch (error) {
      if (error.response?.status === 404) {
        throw new NotFoundException(error.response?.data?.message)
      }
      throw new InternalServerErrorException(error.response?.data || 'Something went wrong.')
    }
  }
}
