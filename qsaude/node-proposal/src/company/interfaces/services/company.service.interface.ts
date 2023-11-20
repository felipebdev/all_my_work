import { CreateCompanyUuidDto, UpdateCompanyDto } from '@app/company/dtos'
import { CompanyEntity } from '@app/company/entities'

export interface ICompanyService {
  findOneBy(filter: Partial<CompanyEntity>): Promise<CompanyEntity>
  create(createCompanyDTO: CreateCompanyUuidDto): Promise<CompanyEntity>
  update(idCompany: string, updateCompanyDto: UpdateCompanyDto): Promise<CompanyEntity>
}
