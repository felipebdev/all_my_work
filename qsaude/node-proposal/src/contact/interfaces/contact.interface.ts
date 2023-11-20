import { ContactType } from '@app/contact/interfaces/contact.enum'
import { ContactRefType } from './contact.enum'

export interface Contact {
  idContact?: string
  type: ContactType
  value: string
  refType: ContactRefType
  refId: string
}

export interface ContactFilter {
  idContact?: string
  type?: ContactType
  value?: string
  refType?: ContactRefType
  refId?: string
}
