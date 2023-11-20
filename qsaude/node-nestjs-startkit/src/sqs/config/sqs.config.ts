import { registerAs } from '@nestjs/config'
import Joi from 'joi'

export const sqsConfig = () =>
  registerAs('sqs', () => {
    const values = {
      accountNumber: process.env.AWS_SQS_ACCOUNT_ID,
      region: process.env.AWS_SQS_DEFAULT_REGION,
      endpoint: process.env.AWS_SQS_QUEUE_ENDPOINT,
      credentials: {
        accessKeyId: process.env.AWS_SQS_ACCESS_KEY_ID,
        secretAccessKey: process.env.AWS_SQS_SECRET_ACCESS_KEY
      },
      consumer: {
        waitTimeSeconds: process.env.AWS_SQS_POLLING_TIME
      }
    }
    const schema = Joi.object({
      accountNumber: Joi.string().required(),
      region: Joi.string().required(),
      endpoint: Joi.string().required(),
      credentials: Joi.object({
        accessKeyId: Joi.string().required(),
        secretAccessKey: Joi.string().required()
      }).required(),
      consumer: Joi.object({
        waitTimeSeconds: Joi.string().required()
      }).required(),
    }).required()
    const { error } = schema.validate(values, { abortEarly: false })
    if (error) {
      throw new Error(error.message)
    }
    return values
  })
