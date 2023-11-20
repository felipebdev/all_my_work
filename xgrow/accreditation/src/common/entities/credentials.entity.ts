import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn } from 'typeorm'

@Entity('credentials')
export class CredentialsEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'id' })
  id: string

  @CreateDateColumn({ name: 'datetime' })
  datetime: Date

  @Column({ name: 'user_id' })
  userId: string

  @Column({ name: 'success' })
  success: boolean

  @Column({ name: 'file' })
  file: string

  @Column({ name: 'company_name' })
  companyName: string

  @Column({ name: 'first_name' })
  firstName: string

  @Column({ name: 'last_name' })
  lastName: string

  @Column({ name: 'type_person' })
  typePerson: string

  @Column({ name: 'document' })
  document: string
}
