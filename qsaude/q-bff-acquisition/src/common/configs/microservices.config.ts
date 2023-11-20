import { registerAs } from '@nestjs/config'
import Joi from 'joi'

export const microsservicesUrlConfig = () =>
  registerAs('ms', () => {
    const values = {
      lead: process.env.LEAD_MS_BASE_URL,
      proposal: process.env.PROPOSAL_MS_BASE_URL,
      token: process.env.TOKEN_MS_BASE_URL,
      parametrization: process.env.PARAMETRIZATION_MS_BASE_URL,
      file: process.env.FILE_MS_BASE_URL,
      fileOrigin: process.env.FILE_ORIGIN,
      zipCode: process.env.ZIP_CODE_MS_BASE_URL
    }
    const schema = Joi.object({
      lead: Joi.string().required(),
      proposal: Joi.string().required(),
      token: Joi.string().required(),
      parametrization: Joi.string().required(),
      file: Joi.string().required(),
      fileOrigin: Joi.string().required(),
      zipCode: Joi.string().required()
    })
    const { error } = schema.validate(values, { abortEarly: false })
    if (error) {
      throw new Error(error.message)
    }
    return values
  })
