export interface IApplicationSettings {
  aws: {
    s3: {
      bucket: string
    }
  }
  storage: {
    path: string
  }
  notification: {
    [key: string]: string
  }
}
