import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { CreateFileDto } from '../dto/create-file.dto'
import { IFiles, IFilesKey } from '../interfaces/files.interface'
import { InjectModel, Model } from 'nestjs-dynamoose'
import { FileDto } from '../dto/file.dto'
import { v4 as uuidv4 } from 'uuid'
import { S3 } from 'aws-sdk'
import { ConfigService } from '@nestjs/config'
import { GetObjectOutput } from 'aws-sdk/clients/s3'

@Injectable()
export class FileService {
  constructor(
    @InjectModel('q-ecomm-files')
    private filesModel: Model<IFiles, IFilesKey>,
    private configService: ConfigService
  ) {
    this.s3BucketName = this.configService.get<string>('s3.bucket')
  }

  s3BucketName: string

  s3 = new S3({
    accessKeyId: this.configService.get<string>('s3.accessKeyId'),
    secretAccessKey: this.configService.get<string>('s3.secretAccessKey')
  })

  async create(createFileDto: CreateFileDto, file: Express.MulterS3.File) {
    const { fileType, idPerson, idProposal, origin } = createFileDto
    const uuid = uuidv4()

    const { Location } = await this.s3
      .upload({
        Bucket: this.s3BucketName,
        Key: uuid,
        Body: file.buffer,
        ContentType: file.mimetype
      })
      .promise()
      .catch((err) => {
        throw new InternalServerErrorException({
          ...err,
          message: `Couldn't upload file to S3 bucket.`
        })
      })

    const createdFile = await this.filesModel
      .create({
        id: uuid,
        fileType,
        idPerson,
        idProposal,
        origin,
        fileUrl: Location,
        fileMimetype: file.mimetype,
        fileOriginalname: file.originalname,
        fileSize: file.size
      })
      .catch((err) => {
        throw new InternalServerErrorException({
          ...err,
          message: `Couldn't save file in DynamoDB.`
        })
      })

    return createdFile
  }

  async findAllByProposalAndPersonAndOrigin(
    origin: string,
    idProposal: string,
    idPerson: string,
    idCompany: string
  ): Promise<FileDto[]> {
    let idPersonOrIdCompany

    if (idPerson)
      idPersonOrIdCompany = {
        idPerson: { contains: idPerson }
      }
    else if (idCompany)
      idPersonOrIdCompany = {
        idCompany: { contains: idCompany }
      }

    const response = await this.filesModel
      .scan({
        origin: { contains: origin },
        idProposal: { contains: idProposal },
        ...idPersonOrIdCompany
      })
      .exec()

    const file = response as FileDto[]
    if (!file.length) throw new NotFoundException(`Files not found for proposal #${idProposal}.`)
    return file
  }

  async findOne(id: string): Promise<FileDto> {
    const response = await this.filesModel.get({ id })
    const file = response as FileDto
    if (!file) throw new NotFoundException(`File #${id} not found.`)
    return file
  }

  async preview(id: string): Promise<GetObjectOutput> {
    const file = await this.s3
      .getObject({ Bucket: this.s3BucketName, Key: id })
      .promise()
      .catch(() => {
        throw new NotFoundException(`File #${id} was not found`)
      })
    return file
  }

  async delete(id: string) {
    const deleted = await this.s3
      .deleteObject({
        Bucket: this.s3BucketName,
        Key: id
      })
      .promise()
    if (!deleted) throw new InternalServerErrorException(`Couldn't remove file from S3 bucket.`)
    await this.filesModel.delete({ id })
  }
}
