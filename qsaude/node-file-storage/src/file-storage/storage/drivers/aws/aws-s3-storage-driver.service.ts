import { IApplicationSettings } from '@app/file-storage/shared/interfaces/application-settings.interface'
import { AbstractStorageDriverService } from '@app/file-storage/storage/drivers/abstract-storage-driver.service'
import { IFile } from '@app/file-storage/storage/interfaces/models/file.interface'
import {
  GetObjectCommand,
  GetObjectCommandOutput,
  PutObjectCommand,
  PutObjectCommandOutput,
  S3Client
} from '@aws-sdk/client-s3'
import { Injectable } from '@nestjs/common'
import { createReadStream } from 'fs'

@Injectable()
export class AwsS3StorageDriverService extends AbstractStorageDriverService {
  constructor(private readonly client: S3Client) {
    super()
  }
  async uploadFile(file: IFile, settings: IApplicationSettings): Promise<PutObjectCommandOutput> {
    const fileStream = createReadStream(file.path)
    const command = new PutObjectCommand({
      Bucket: settings.aws.s3.bucket,
      Key: `${settings.storage.path}/${file.filename}`,
      Body: fileStream,
      ContentLength: file.size,
      ContentType: file.mimetype
    })
    return this.client.send(command)
  }
  async downloadFile(fileKey: string, settings: IApplicationSettings): Promise<GetObjectCommandOutput> {
    const command = new GetObjectCommand({
      Bucket: settings.aws.s3.bucket,
      Key: `${settings.storage.path}/${fileKey}`
    })
    return this.client.send(command)
  }
}
