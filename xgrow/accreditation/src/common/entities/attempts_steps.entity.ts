import { AttemptEntity } from '@app/common/entities'
import { Column, Entity, JoinColumn, OneToOne, PrimaryGeneratedColumn } from 'typeorm'

@Entity('attempts_steps')
export class AttemptsStepsEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'id' })
  id: string

  @JoinColumn({ name: 'attempt_id' })
  @OneToOne(() => AttemptEntity)
  attemptId: string

  @Column({ name: 'step' })
  step: string

  @Column({ name: 'service' })
  service: string

  @Column({ name: 'success' })
  success: boolean

  @Column({ name: 'details' })
  details: string
}
