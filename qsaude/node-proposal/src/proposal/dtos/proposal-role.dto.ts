import { ProposalRole } from '@app/proposal/interfaces'
import { Type } from 'class-transformer'
import { IsNotEmpty, IsString, IsOptional, IsUUID, ValidateNested, IsNotEmptyObject, IsEnum } from 'class-validator'

export class CreateProposalRoleDTO {
  @IsNotEmpty()
  @IsString()
  @IsEnum(ProposalRole)
  role: string

  @IsNotEmpty()
  @IsString()
  idProposal: string

  @IsNotEmpty()
  @IsString()
  idPerson: string
}

export class CreateProposalRoleUuidDto extends CreateProposalRoleDTO {
  @IsOptional()
  @IsUUID()
  idProposalRole: string
}

export class BaseCreateProposalRoleUuidDTO {
  @IsNotEmptyObject()
  @ValidateNested({ each: true })
  @Type(() => CreateProposalRoleUuidDto)
  proposalRole: CreateProposalRoleUuidDto
}
