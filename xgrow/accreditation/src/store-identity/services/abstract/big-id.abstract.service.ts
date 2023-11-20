import { IBigIDService } from '@app/store-identity/interfaces/services'

export abstract class AbstractBigIdService implements IBigIDService {
  abstract ocr(base64File: string): Promise<string>
}
