import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn, UpdateDateColumn } from 'typeorm'
import { ProposalClass } from '../interfaces/proposal.enum'

@Entity('Proposal')
export class ProposalEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'idProposal' })
  idProposal: string

  @Column({ name: 'idLead' })
  idLead: string

  @Column({ name: 'ProposalNumber' })
  proposalNumber: string

  @Column({ name: 'LevelSale' })
  levelSale: number

  @CreateDateColumn({ name: 'createdAt' })
  createdAt: Date

  @UpdateDateColumn({ name: 'updatedAt' })
  updatedAt: Date
}
