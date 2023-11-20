import { ContactType } from '@app/contact/interfaces/contact.enum'
import { Column, Entity, PrimaryGeneratedColumn } from 'typeorm'
import { ContactRefType } from '../interfaces/contact.enum'

@Entity('Contact')
export class ContactEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'idContact' })
  idContact: string

  @Column({ name: 'Type', type: 'enum', enum: ContactType })
  type: ContactType

  @Column({ name: 'Value' })
  value: string

  @Column({ name: 'RefType', type: 'enum', enum: ContactRefType })
  refType: ContactRefType

  @Column({ name: 'RefId' })
  refId: string
}
