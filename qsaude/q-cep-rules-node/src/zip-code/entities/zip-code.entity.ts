import { Column, CreateDateColumn, Entity, PrimaryGeneratedColumn, UpdateDateColumn } from 'typeorm'

@Entity('ZipCode')
export class ZipCodeEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'idZipCode' })
  idZipCode: string

  @Column({ name: 'ZipCode' })
  zipCode: string

  @Column({ name: 'Address' })
  address: string

  @Column({ name: 'District' })
  district: string

  @Column({ name: 'state' })
  state: string

  @Column({ name: 'City' })
  city: string

  @Column({ name: 'IBGECode' })
  ibgeCode: string

  @CreateDateColumn({ name: 'CreatedAt' })
  createdAt: Date

  @UpdateDateColumn({ name: 'UpdatedAt' })
  updatedAt: Date
}
