import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn, UpdateDateColumn } from 'typeorm'

@Entity('Person')
export class PersonEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'idPerson' })
  idPerson: string

  @Column({ name: 'Name' })
  name: string

  @Column({ name: 'SocialName' })
  socialName: string

  @Column({ name: 'Birthday' })
  birthday: string

  @Column({ name: 'Gender' })
  gender: string

  @Column({ name: 'MaritalStatus' })
  maritalStatus: string

  @Column({ name: 'CPF' })
  cpf: string

  @Column({ name: 'CNS' })
  cns: string

  @Column({ name: 'RG' })
  rg: string

  @Column({ name: 'EmittingOrgan' })
  emittingOrgan: string

  @Column({ name: 'MotherName' })
  motherName: string

  @CreateDateColumn({ name: 'createdAt' })
  createdAt: Date

  @UpdateDateColumn({ name: 'updatedAt' })
  updatedAt: Date
}
