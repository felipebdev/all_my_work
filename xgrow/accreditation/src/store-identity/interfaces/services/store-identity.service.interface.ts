import { UserDocumentsDTO } from '@app/store-identity/dto'

export interface IStoreIdentityService {
  validateDocuments(
    file: Express.Multer.File,
    userDocuments: UserDocumentsDTO,
    correlationId: string,
    { platform_id, user_id }: { platform_id: string; user_id: string }
  ): Promise<boolean>
}
