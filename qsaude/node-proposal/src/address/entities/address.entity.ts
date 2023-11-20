import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn, UpdateDateColumn } from 'typeorm'

@Entity('Address')
export class AddressEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'idAddress' })
  uuidAddress: string

  @Column({ name: 'ZipCode' })
  zipCode: string

  @Column({ name: 'Address' })
  address: string

  @Column({ name: 'AddressNumber' })
  addressNumber: string

  @Column({ name: 'AddressComplement' })
  addressComplement: string

  @Column({ name: 'Neighborhood' })
  neighborhood: string

  @Column({ name: 'City' })
  city: string

  @Column({ name: 'State' })
  state: string

  @CreateDateColumn({ name: 'createdAt' })
  createdAt: Date

  @UpdateDateColumn({ name: 'updatedAt' })
  updatedAt: Date
}
