import { Column, Entity, PrimaryGeneratedColumn } from 'typeorm'

@Entity('bank_credentials')
export class BankCredentialsEntity {
  @PrimaryGeneratedColumn('uuid', { name: 'id' })
  id: string

  @Column({ name: 'credentials_id' })
  credentialsId: string

  @Column({ name: 'bank_code' })
  bankCode: string

  @Column({ name: 'agency' })
  agency: string

  @Column({ name: 'agency_digit' })
  agencyDigit: string

  @Column({ name: 'account' })
  account: string

  @Column({ name: 'account_digit' })
  accountDigit: string

  @Column({ name: 'account_type' })
  accountType: string

  @Column({ name: 'legal_name' })
  legalName: string
}
