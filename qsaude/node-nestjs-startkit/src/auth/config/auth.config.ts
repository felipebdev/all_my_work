import { IAuthenticateConfig } from '@app/auth/interfaces'
import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export const authConfig = registerAs('auth', (): IAuthenticateConfig => {
  const values: IAuthenticateConfig = {
    jwt: {
      jwksUri: process.env.AUTH_JWKS_URI_PJ,
      audience: process.env.AUTH_JWT_AUDIENCE,
      issuer: process.env.AUTH_JWT_ISSUER
    }
  }

  const schema = Joi.object({
    jwt: Joi.object({
      jwksUri: Joi.string().required(),
      audience: Joi.string().required(),
      issuer: Joi.string().required()
    }).required()
  }).required()

  const { error } = schema.validate(values, { abortEarly: false })

  if (error) {
    throw new Error(error.message)
  }
  
  return values
})
