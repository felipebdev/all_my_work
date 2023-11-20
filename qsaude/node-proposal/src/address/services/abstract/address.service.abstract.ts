import { ProposalRole } from '@app/proposal/interfaces'
import { CreatePersonAddressDto, UpdateAddressDto, CreateCompanyAddressDto } from '../../dtos/address.dto'
import { IAddressService } from '../../interfaces/services/address.service.interface'

export abstract class AbstractAddressService implements IAddressService {
  abstract createPersonAddress(idProposal: string, role: ProposalRole, addressDto: CreatePersonAddressDto)
  abstract createCompanyAddress(addressDto: CreateCompanyAddressDto)
  abstract findOne(id: string)
  abstract findOneByProposalRole(idProposal: string, role: ProposalRole)
  abstract findOneByCompany(idCompany: string)
  abstract update(id: string, addressDto: UpdateAddressDto)
}
