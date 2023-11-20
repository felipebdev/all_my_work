import { Args, Mutation, Parent, Query, ResolveField, Resolver } from '@nestjs/graphql'
import { CompanyService } from '@app/company/services'
import { Company } from '@app/company/models'
import { CompanyInput, UpdateCompanyInput } from '@app/company/models'
import { Address } from '@app/address/models/address.model'

@Resolver(() => Company)
export class CompanyResolver {
  constructor(private readonly companyService: CompanyService) {}

  @Query(() => Company, { nullable: false })
  async company(@Args('idProposal', { type: () => String }) id: string): Promise<Company> {
    return this.companyService.getByProposal(id)
  }

  @Mutation(() => Company, { nullable: false })
  createCompany(@Args('company', { type: () => CompanyInput }) company: CompanyInput): Promise<Company> {
    return this.companyService.create(company)
  }

  @Mutation(() => Company, { nullable: false })
  updateCompany(
    @Args('id', { type: () => String }) id: string,
    @Args('company', { type: () => UpdateCompanyInput }) company: UpdateCompanyInput
  ): Promise<Company> {
    return this.companyService.update(id, company)
  }

  @ResolveField('address', () => Address, { nullable: true })
  async address(@Parent() company: Company): Promise<Address> {
    return this.companyService.getCompanyAddress(company.idCompany) || ({} as Address)
  }
}
