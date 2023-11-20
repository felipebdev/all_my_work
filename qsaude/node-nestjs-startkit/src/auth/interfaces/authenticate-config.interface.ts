export interface IAuthenticateConfig {
    jwt: {
      jwksUri: string
      audience: string
      issuer: string
    }
  }
  