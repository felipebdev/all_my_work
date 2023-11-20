export type BigBoostAuthenticateResponse = {
  expiration: string
  message: string
  success: boolean
  token: string
  tokenID: string
}

export type BigBoostAuthenticateBody = {
  login: string
  password: string
  expires: number
}

export type BigBoostCompaniesSearchBody = {
  Datasets: string
  q: string
  AccessToken: string
}

export enum BigBoostRoutes {
  AUTHENTICATE = '/tokens/generate',
  COMPANIES = '/companies',
  PEOPLE = '/peoplev2'
}

export enum BigBoostExceptions {
  AUTHENTICATE_ERROR = 'Erro ao obter token BigBoost'
}

export type BigBoostBasicDataResponse = {
  Result: Array<{
    MatchKeys: string
    BasicData: {
      TaxIdNumber: string
      TaxIdCountry: string
      AlternativeIdNumbers: object
      ExtendedDocumentInformation: {
        RG: {
          DocumentLast4Digits: string
          CreationDate: Date
          LastUpdateDate: Date
          Sources: string[]
        }
      }
      Name: string
      Aliases: {
        CommonName: string
        StandardizedName: string
      }
      Gender: string
      NameWordCount: number
      NumberOfFullNameNamesakes: number
      NameUniquenessScore: number
      FirstNameUniquenessScore: string
      FirstAndLastNameUniquenessScore: string
      BirthDate: string
      Age: number
      ZodiacSign: string
      ChineseSign: string
      BirthCountry: string
      MotherName: string
      FatherName: string
      MaritalStatusData: object
      TaxIdStatus: string
      TaxIdOrigin: string
      TaxIdFiscalRegion: string
      HasObitIndication: false
      TaxIdStatusDate: string
      CreationDate: Date
      LastUpdateDate: string
    }
  }>
  QueryId: string
  ElapsedMilliseconds: number
  QueryDate: Date
  Status: {
    basic_data: Array<{
      Code: number
      Message: string
    }>
  }
  Evidences: object
}

export type BigBoostRelationshipsResponse = {
  Result: [
    {
      MatchKeys: string
      Relationships: {
        Relationships: Array<{
          RelatedEntityTaxIdNumber: string
          RelatedEntityTaxIdType: string
          RelatedEntityTaxIdCountry: string
          RelatedEntityName: string
          RelationshipType: string
          RelationshipName: string
          RelationshipLevel: string
          RelationshipDataOrigin: string
          CreationDate: Date
          LastUpdateDate: Date
          RelationshipStartDate: Date
          RelationshipEndDate: Date
        }>
        IsFamilyCompany: boolean
        IsFamilyOperated: boolean
        TotalRelationships: number
        TotalOwners: number
        TotalEmployees: number
        TotalOwned: number
      }
    }
  ]
  QueryId: string
  ElapsedMilliseconds: number
  QueryDate: Date
  Status: {
    relationships: Array<{
      Code: number
      Message: string
    }>
  }
  Evidences: object
}
