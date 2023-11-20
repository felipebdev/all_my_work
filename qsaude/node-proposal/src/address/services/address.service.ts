import { Injectable, InternalServerErrorException, NotFoundException } from '@nestjs/common'
import { InjectRepository } from '@nestjs/typeorm'
import { Repository } from 'typeorm'
import { AddressEntity } from '../entities/address.entity'
import { AbstractAddressService } from './abstract/address.service.abstract'
import { AbstractContactService } from '@app/contact/services/abstract/contact.service.abstract'
import { UpdateAddressDto, CreatePersonAddressDto, CreateCompanyAddressDto } from '@app/address/dtos'
import { ContactRefType, ContactType } from '@app/contact/interfaces/contact.enum'
import { ContactFilter } from '@app/contact/interfaces/contact.interface'
import { ProposalRole } from '@app/proposal/interfaces'
import { AbstractProposalRoleService } from '@app/proposal/services'

@Injectable()
export class AddressService implements AbstractAddressService {
  constructor(
    @InjectRepository(AddressEntity)
    private readonly addressRepository: Repository<AddressEntity>,
    private readonly contactService: AbstractContactService,
    private readonly proposalRoleService: AbstractProposalRoleService
  ) {}

  async findOne(uuidAddress: string): Promise<AddressEntity> {
    const address = await this.addressRepository.findOne({ where: { uuidAddress } })
    if (!address) throw new NotFoundException(`Address #${uuidAddress} was not found`)
    return address
  }

  async findOneByProposalRole(idProposal: string, role: ProposalRole): Promise<AddressEntity> {
    const { idProposalRole } = await this.proposalRoleService.findOneBy({ idProposal, role })
    const contactFilter: ContactFilter = {
      refId: idProposalRole,
      refType: ContactRefType.proposalRole,
      type: ContactType.address
    }
    const { value: idAddress } = await this.contactService.findOneBy(contactFilter)

    return this.findOne(idAddress)
  }

  async createPersonAddress(
    idProposal: string,
    role: ProposalRole,
    addressDto: CreatePersonAddressDto
  ): Promise<AddressEntity> {
    try {
      const { idProposalRole } = await this.proposalRoleService.findOneBy({ idProposal, role })
      const address = this.addressRepository.create(addressDto)
      const createdAddress = await this.addressRepository.save(address)
      await this.contactService.create({
        type: ContactType.address,
        value: createdAddress.uuidAddress,
        refType: ContactRefType.proposalRole,
        refId: idProposalRole
      })
      return createdAddress
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }

  async createCompanyAddress(companyAddressDto: CreateCompanyAddressDto): Promise<AddressEntity> {
    try {
      const { idCompany, ...addressDto } = companyAddressDto
      const address = this.addressRepository.create(addressDto)
      const createdAddress = await this.addressRepository.save(address)
      await this.contactService.create({
        type: ContactType.address,
        value: createdAddress.uuidAddress,
        refType: ContactRefType.company,
        refId: idCompany
      })
      return createdAddress
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }

  async findOneByCompany(idCompany: string): Promise<AddressEntity> {
    const contactFilter: ContactFilter = {
      refId: idCompany,
      refType: ContactRefType.company,
      type: ContactType.address
    }
    const { value: idAddress } = await this.contactService.findOneBy(contactFilter)

    return this.findOne(idAddress)
  }

  async update(uuidAddress: string, addressDto: UpdateAddressDto) {
    try {
      const address = await this.addressRepository.preload({
        uuidAddress,
        ...addressDto
      })

      return await this.addressRepository.save(address)
    } catch (error) {
      throw new InternalServerErrorException(error)
    }
  }
}
