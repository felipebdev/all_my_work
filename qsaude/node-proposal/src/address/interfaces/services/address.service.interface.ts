import { ProposalRole } from '@app/proposal/interfaces'
import { CreateCompanyAddressDto, CreatePersonAddressDto, UpdateAddressDto } from '../../dtos/address.dto'

export interface IAddressService {
  createPersonAddress(idProposal: string, role: ProposalRole, addressDto: CreatePersonAddressDto)
  findOne(id: string)
  findOneByProposalRole(idProposal: string, role: ProposalRole)
  update(id: string, addressDto: UpdateAddressDto)
  findOneByCompany(idCompany: string)
  createCompanyAddress(addressDto: CreateCompanyAddressDto)
}
