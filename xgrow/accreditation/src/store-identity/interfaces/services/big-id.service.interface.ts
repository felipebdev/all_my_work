export interface IBigIDService {
  ocr(base64File: string): Promise<string>
}
