export interface IHandlerGetFile {
  handleGetFile(appKey: string, fileKey: string): Promise<any>
}
