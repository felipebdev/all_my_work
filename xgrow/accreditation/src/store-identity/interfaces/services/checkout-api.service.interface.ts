export interface ICheckoutAPIService {
  createRecipient(platformId: string, userId: string, correlationId: string)
}
