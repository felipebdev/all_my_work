import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export const externalMSsConfig = () =>
  registerAs('external', () => {
    const values = {
      viaCep: process.env.VIA_CEP_BASE_URL
    }
    const schema = Joi.object({
      viaCep: Joi.string().required()
    }).required()
    const { error } = schema.validate(values, { abortEarly: false })
    if (error) {
      throw new Error(error.message)
    }
    return values
  })
