import { CompanySize } from '@app/company/interfaces'
import {
  Column,
  CreateDateColumn,
  Entity,
  JoinColumn,
  OneToOne,
  PrimaryGeneratedColumn,
  UpdateDateColumn
} from 'typeorm'
import { ProposalEntity } from '@app/proposal/entities'

@Entity('Company')
export class CompanyEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'idCompany' })
  idCompany: string

  @Column({ name: 'CNPJ' })
  cnpj: string

  @Column({ name: 'CompanyName' })
  name: string

  @Column({ name: 'TradingName' })
  tradeName: string

  @Column({ name: 'CodeLegalNature' })
  codeLegalNature: string

  @Column({ name: 'CNAE' })
  cnae: string

  @Column({ name: 'CompanySize', type: 'enum', enum: CompanySize })
  companySize: CompanySize

  @Column({ name: 'OpeningDate', type: 'date' })
  openingDate: Date

  @Column({ name: 'idProposal' })
  @JoinColumn({ name: 'idProposal' })
  @OneToOne(() => ProposalEntity)
  idProposal: string

  @CreateDateColumn({ name: 'CreatedAt' })
  createdAt: Date

  @UpdateDateColumn({ name: 'UpdatedAt' })
  updatedAt: Date
}
