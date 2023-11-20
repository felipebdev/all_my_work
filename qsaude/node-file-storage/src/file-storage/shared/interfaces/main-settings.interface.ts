import { IAwsSettings } from '@app/file-storage/shared/interfaces/aws-settings.interface'

export interface IMainSettings {
  name: string
  description: string
  version: string
  port: number
  aws: IAwsSettings
}
