import { Module } from '@nestjs/common'
import { AddressResolver } from '@app/address/resolvers/address.resolver'
import { AddressService } from '@app/address/services/address.service'

@Module({
  providers: [AddressResolver, AddressService]
})
export class AddressModule {}
