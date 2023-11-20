import { ICheckoutAPIService } from '@app/store-identity/interfaces/services'

export abstract class AbstractCheckoutAPIService implements ICheckoutAPIService {
  abstract createRecipient(platformId: string, userId: string, correlationId: string)
}
