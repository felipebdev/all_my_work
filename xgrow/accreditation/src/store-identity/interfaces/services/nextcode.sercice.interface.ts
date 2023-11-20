export interface INextCodeService {
  ocr(fileName: string, base64File: string): Promise<string>
}
