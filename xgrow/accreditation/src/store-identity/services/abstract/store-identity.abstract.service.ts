import { UserDocumentsDTO } from '@app/store-identity/dto'
import { IStoreIdentityService } from '@app/store-identity/interfaces/services'

export abstract class AbstractStoreIdentityService implements IStoreIdentityService {
  abstract validateDocuments(
    file: Express.Multer.File,
    userDocuments: UserDocumentsDTO,
    correlationId: string,
    { platform_id, user_id }: { platform_id: string; user_id: string }
  ): Promise<boolean>
}
