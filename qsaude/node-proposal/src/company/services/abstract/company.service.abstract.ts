import { ICompanyService } from '@app/company/interfaces'
import { CreateCompanyUuidDto, UpdateCompanyDto } from '@app/company/dtos'
import { CompanyEntity } from '@app/company/entities'
export abstract class AbstractCompanyService implements ICompanyService {
  abstract findOneBy(filter: Partial<CompanyEntity>): Promise<CompanyEntity>
  abstract create(createCompanyDTO: CreateCompanyUuidDto): Promise<CompanyEntity>
  abstract update(idCompany: string, updateCompanyDto: UpdateCompanyDto): Promise<CompanyEntity>
}
