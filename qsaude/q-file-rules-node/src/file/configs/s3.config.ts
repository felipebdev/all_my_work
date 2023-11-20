import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export const s3Config = () =>
  registerAs('s3', () => {
    const values = {
      accessKeyId: process.env.S3_FILE_ACESS_KEY,
      secretAccessKey: process.env.S3_FILE_SECRET,
      bucket: process.env.S3_FILE_BUCKET,
      fileSize: process.env.S3_FILE_SIZE
    }
    const schema = Joi.object({
      accessKeyId: Joi.string().required(),
      secretAccessKey: Joi.string().required(),
      bucket: Joi.string().required(),
      fileSize: Joi.number().required()
    })
    const { error } = schema.validate(values, { abortEarly: false })
    if (error) {
      throw new Error(error.message)
    }
    return values
  })
