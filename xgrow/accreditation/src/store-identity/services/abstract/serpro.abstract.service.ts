import { ISerproService } from '@app/store-identity/interfaces/services'

export abstract class AbstractSerproService implements ISerproService {
  abstract validateRelationsSerpro(cnpj: string, cpf: string, ownerName: string): Promise<boolean>
}
