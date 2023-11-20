import { NextCodeValidatedDocument } from '@app/store-identity/interfaces'

export const validatedDocumentMock: NextCodeValidatedDocument = {
  id: 'string',
  version: 'string',
  data: [
    {
      extraction: {
        schemaName: 'string',

        person: {
          taxId: '999999999/99',
          name: 'string',
          birthdate: 'string',
          parentage: 'string'
        },

        otherFields: {
          firstIssuedAt: 'string',
          mopedsLicense: 'string',
          driversLicenseCategory: 'string',
          issuedAt: 'string',
          formNumberFront: 'string',
          formNumberBack: 'string',
          locale: 'string',
          securityNumber: 'string',
          notes: 'string',
          permission: 'string',
          registerNumber: 'string',
          renach: 'string',
          state: 'string',
          expireAt: 'string',
          sourceDocument: 'string',
          sourceDocumentIssuer: 'string'
        }
      },

      enhanced: {
        schemaName: 'string',

        person: {
          taxId: 'string',
          name: 'string',
          birthdate: 'string',
          mothersName: 'string',
          fathersName: 'string'
        },

        otherFields: {
          firstIssuedAt: 'string',
          mopedsLicense: 'string',
          driversLicenseCategory: 'string',
          issuedAt: 'string',
          formNumberFront: 'string',
          formNumberBack: 'string',
          locale: 'string',
          securityNumber: 'string',
          notes: 'string',
          permission: 'string',
          registerNumber: 'string',
          renach: 'string',
          state: 'string',
          expireAt: 'string',
          sourceDocument: 'string',
          sourceDocumentIssuer: 'string'
        }
      },

      taxData: {
        taxId: 'string',
        name: 'string',
        mothersName: 'string',
        birthdate: 'string'
      },

      matches: {
        name: true,
        mothersName: true,
        birthdate: true
      },

      classification: {
        type: 'string',
        subtype: 'string',
        country: 'string',
        side: 'string',
        sameImage: true
      }
    }
  ],
  metadata: {
    filesInfo: [
      {
        fieldname: 'string',
        name: 'string',
        size: 2,
        pages: 2,
        mimetype: 'string',
        encoding: 'string',
        sha256: 'string',
        details: [
          {
            side: 'string,',
            confidence: 2,
            page: 2
          }
        ]
      }
    ],
    timeSpent: 2
  }
}
