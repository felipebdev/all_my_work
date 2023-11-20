import { Bucket, Storage } from '@google-cloud/storage'
import { BadRequestException, Injectable } from '@nestjs/common'
import { parse } from 'path'
import { File } from '@app/common/interfaces'
import { ConfigService } from '@nestjs/config'

@Injectable()
export class GCPStorageService {
  private bucket: Bucket
  private storage: Storage

  constructor(private readonly configService: ConfigService) {
    this.storage = new Storage()
    const bucket = configService.get<string>('gcps.bucketName')
    this.bucket = this.storage.bucket(bucket)
  }

  private setDestination(destination: string): string {
    let escDestination = ''
    escDestination += destination.replace(/^\.+/g, '').replace(/^\/+|\/+$/g, '')
    if (escDestination !== '') escDestination = escDestination + '/'
    return escDestination
  }

  private setFilename(uploadedFile: File): string {
    const fileName = parse(uploadedFile.originalname)
    return `${fileName.name}-${Date.now()}${fileName.ext}`
      .replace(/^\.+/g, '')
      .replace(/^\/+/g, '')
      .replace(/\r|\n/g, '_')
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  async uploadFile(uploadedFile: File, destination: string): Promise<any> {
    const fileName = this.setDestination(destination) + this.setFilename(uploadedFile)
    const file = this.bucket.file(fileName)
    try {
      await file.save(uploadedFile.buffer, { contentType: uploadedFile.mimetype })
    } catch (error) {
      throw new BadRequestException(error?.message)
    }
    return { fileName: file.name }
  }

  async removeFile(fileName: string): Promise<void> {
    const file = this.bucket.file(fileName)
    try {
      await file.delete()
    } catch (error) {
      throw new BadRequestException(error?.message)
    }
  }
}
