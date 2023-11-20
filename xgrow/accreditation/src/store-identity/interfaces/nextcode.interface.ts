export enum Routes {
  OCR = '/full-ocr/v4'
}

type ValidatedDocumentData = {
  extraction: {
    schemaName: string

    person: {
      taxId: string
      name: string
      birthdate: string
      parentage: string
    }

    otherFields: {
      firstIssuedAt: string
      mopedsLicense: string
      driversLicenseCategory: string
      issuedAt: string
      formNumberFront: string
      formNumberBack: string
      locale: string
      securityNumber: string
      notes: string
      permission: string
      registerNumber: string
      renach: string
      state: string
      expireAt: string
      sourceDocument: string
      sourceDocumentIssuer: string
    }
  }

  enhanced: {
    schemaName: string

    person: {
      taxId: string
      name: string
      birthdate: string
      mothersName: string
      fathersName: string
    }

    otherFields: {
      firstIssuedAt: string
      mopedsLicense: string
      driversLicenseCategory: string
      issuedAt: string
      formNumberFront: string
      formNumberBack: string
      locale: string
      securityNumber: string
      notes: string
      permission: string
      registerNumber: string
      renach: string
      state: string
      expireAt: string
      sourceDocument: string
      sourceDocumentIssuer: string
    }
  }

  taxData: {
    taxId: string
    name: string
    mothersName: string
    birthdate: string
  }

  matches: {
    name: boolean
    mothersName: boolean
    birthdate: boolean
  }

  classification: {
    type: string
    subtype: string
    country: string
    side: string
    sameImage: boolean
  }
}

type ValidatedDocumentMetadata = {
  filesInfo: [
    {
      fieldname: string
      name: string
      size: number
      pages: number
      mimetype: string
      encoding: string
      sha256: string
      details: Array<{
        side: string
        confidence: number
        page: number
      }>
    }
  ]
  timeSpent: number
}

export interface NextCodeValidatedDocument {
  id: string
  version: string
  data: Array<ValidatedDocumentData>
  metadata: ValidatedDocumentMetadata
}

export enum NextCodeIDExceptionsMessages {
  NOCPF = 'A imagem enviada não contém o número de CPF ou é de baixa qualidade',
  DIFFERENT_DOCUMENT = 'O Documento informado não pertence ao usuário'
}
