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

@Entity('Finance')
export class FinanceEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'idFinance' })
  idFinance: string

  @Column({ name: 'FormPayment' })
  formPayment: string

  @Column({ name: 'DueDate' })
  dueDate: string

  @Column({ name: 'StartingDate', type: 'date' })
  startingDate: Date

  @Column({ name: 'idProposal' })
  @JoinColumn({ name: 'idProposal' })
  @OneToOne(() => ProposalEntity)
  idProposal: string

  @CreateDateColumn({ name: 'CreatedAt' })
  createdAt: Date

  @UpdateDateColumn({ name: 'UpdatedAt' })
  updatedAt: Date
}
