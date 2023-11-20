import { IBigBoostService } from '@app/store-identity/interfaces/services'

export abstract class AbstractBigBoostService implements IBigBoostService {
  abstract validateRelationship(cnpj: string, cpf: string): Promise<boolean>
  abstract personalData(cpf: string): Promise<object>
}
