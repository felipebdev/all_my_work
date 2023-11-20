import { Address } from '@app/address/models/address.model'
import { ZipCodeService } from '@app/zip-code/services/zip-code.service'
import { Args, Query, Resolver } from '@nestjs/graphql'

@Resolver()
export class ZipCodeResolver {
  constructor(private readonly zipCodeService: ZipCodeService) {}

  @Query(() => Address)
  zipCode(@Args('value', { type: () => String }) zipCodeInput: string) {
    return this.zipCodeService.getAddress(zipCodeInput)
  }
}
