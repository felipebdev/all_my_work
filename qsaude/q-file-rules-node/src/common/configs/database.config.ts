import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export const databaseConfig = () =>
  registerAs('database', () => {
    const values = {
      region: process.env.DB_FILE_REGION,
      accessKeyId: process.env.DB_FILE_ACESS_KEY,
      secretAccessKey: process.env.DB_FILE_SECRET
    }
    const schema = Joi.object({
      region: Joi.string().required(),
      accessKeyId: Joi.string().required(),
      secretAccessKey: Joi.string().required()
    })
    const { error } = schema.validate(values, { abortEarly: false })
    if (error) {
      throw new Error(error.message)
    }
    return values
  })
