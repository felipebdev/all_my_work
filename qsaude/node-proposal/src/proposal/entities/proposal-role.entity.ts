import {
  Column,
  Entity,
  JoinColumn,
  OneToOne,
  PrimaryGeneratedColumn,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne
} from 'typeorm'
import { ProposalEntity } from './proposal.entity'
import { PersonEntity } from '../../person/entities/person.entity'

@Entity('ProposalRole')
export class ProposalRoleEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'idProposalRole' })
  idProposalRole: string

  @Column({ name: 'Role' })
  role: string

  @JoinColumn({ name: 'idProposal' })
  @ManyToOne(() => ProposalEntity)
  idProposal: string

  @JoinColumn({ name: 'idPerson' })
  @ManyToOne(() => PersonEntity)
  idPerson: string

  @CreateDateColumn({ name: 'CreatedAt' })
  createdAt: Date

  @UpdateDateColumn({ name: 'UpdatedAt' })
  updatedAt: Date
}
