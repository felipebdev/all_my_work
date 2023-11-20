import { AddressArgs, CompanyAddressInput, PersonAddressInput } from '@app/address/models/address.input.model'
import { Address } from '@app/address/models/address.model'
import { Args, Mutation, Query, Resolver } from '@nestjs/graphql'
import { AddressService } from '@app/address/services/address.service'

@Resolver()
export class AddressResolver {
  constructor(private readonly addressService: AddressService) {}

  @Query(() => Address, { nullable: false })
  async personAddress(@Args() addressArgs: AddressArgs): Promise<Address> {
    return this.addressService.getAddressByPersonId(addressArgs)
  }

  @Mutation(() => Address, { nullable: false })
  async createPersonAddress(
    @Args('address', { type: () => PersonAddressInput }) personAddressInput: PersonAddressInput
  ): Promise<Address> {
    return this.addressService.createPersonAddress(personAddressInput)
  }

  @Mutation(() => Address, { nullable: false })
  async createCompanyAddress(
    @Args('address', { type: () => CompanyAddressInput }) companyAddressInput: CompanyAddressInput
  ): Promise<Address> {
    return this.addressService.createCompanyAddress(companyAddressInput)
  }
}
