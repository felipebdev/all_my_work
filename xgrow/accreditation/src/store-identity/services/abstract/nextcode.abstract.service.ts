import { INextCodeService } from '@app/store-identity/interfaces/services'

export abstract class AbstractNextcodeService implements INextCodeService {
  abstract ocr(fileName: string, base64File: string): Promise<string>
}
