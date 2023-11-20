export interface IBigBoostService {
  validateRelationship(cnpj: string, cpf: string): Promise<boolean>
  personalData(cpf: string): Promise<object>
}
