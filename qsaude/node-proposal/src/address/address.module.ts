import { Module } from '@nestjs/common'
import { TypeOrmModule } from '@nestjs/typeorm'
import { AddressEntity } from '@app/address/entities'
import { AddressService } from '@app/address/services'
import { AddressController } from '@app/address/controllers'
import { AbstractAddressService } from '@app/address/services/abstract/address.service.abstract'
import { ContactModule } from '../contact/contact.module'
import { ProposalModule } from '@app/proposal/proposal.module'

@Module({
  imports: [TypeOrmModule.forFeature([AddressEntity]), ContactModule, ProposalModule],
  providers: [{ provide: AbstractAddressService, useClass: AddressService }],
  controllers: [AddressController]
})
export class AddressModule {}
