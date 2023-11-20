import { CreateCompanyUuidDto, UpdateCompanyDto } from '@app/company/dtos'
import { CompanyEntity } from '@app/company/entities'
import { AbstractCompanyService } from '@app/company/services'
import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'

@Injectable()
export class CompanyService implements AbstractCompanyService {
  constructor(
    @InjectRepository(CompanyEntity)
    private readonly companyRepository: Repository<CompanyEntity>
  ) {}

  async findOneBy(filter: Partial<CompanyEntity>): Promise<CompanyEntity> {
    const company = await this.companyRepository.findOneBy(filter)
    if (!company) throw new NotFoundException(`Company was not found`)
    return company
  }

  async create(createCompanyDTO: CreateCompanyUuidDto): Promise<CompanyEntity> {
    try {
      const company = this.companyRepository.create(createCompanyDTO)
      return await this.companyRepository.save(company)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }
  async update(idCompany: string, updateCompanyDto: UpdateCompanyDto): Promise<CompanyEntity> {
    try {
      const company = await this.companyRepository.preload({
        idCompany,
        ...updateCompanyDto
      })
      if (!company) throw new NotFoundException('Company was not found')
      return await this.companyRepository.save(company)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }
}
