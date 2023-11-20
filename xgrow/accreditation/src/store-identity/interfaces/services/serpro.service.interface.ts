export interface ISerproService {
  validateRelationsSerpro(cnpj: string, cpf: string, ownerName: string | null): Promise<boolean>
}
