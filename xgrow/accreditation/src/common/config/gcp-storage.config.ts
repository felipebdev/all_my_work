import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export const googleStorageConfig = () =>
  registerAs('gcps', () => {
    const values = {
      bucketName: process.env.GCP_STORAGE_BUCKET_NAME
    }

    const schema = Joi.object({
      bucketName: Joi.string().required()
    }).required()

    const { error } = schema.validate(values, { abortEarly: false })
    if (error) {
      throw new Error(error.message)
    }
    return values
  })
