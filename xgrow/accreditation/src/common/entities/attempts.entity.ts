import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn } from 'typeorm'

@Entity('attempts')
export class AttemptEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'id' })
  id: string

  @Column({ name: 'credentials_id' })
  credentialsId: string

  @CreateDateColumn({ name: 'datetime' })
  datetime: Date

  @Column({ name: 'success' })
  success: boolean
}
