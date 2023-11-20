import { Type } from 'class-transformer'
import { ProposalClass } from '@app/proposal/interfaces'
import {
  IsNotEmpty,
  IsString,
  IsOptional,
  IsUUID,
  ValidateNested,
  IsNotEmptyObject,
  IsDate,
  IsEnum,
  IsNumber
} from 'class-validator'

export class CreateProposalDTO {
  @IsOptional()
  @IsString()
  idLead?: string

  @IsOptional()
  @IsNumber()
  levelSale?: number
}

export class CreateProposalUuidDto extends CreateProposalDTO {
  @IsOptional()
  @IsUUID()
  idProposal?: string
}

export class BaseCreateProposalUuidDTO {
  @IsNotEmptyObject()
  @ValidateNested({ each: true })
  @Type(() => CreateProposalUuidDto)
  proposal: CreateProposalUuidDto
}
