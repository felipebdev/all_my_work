import { IMainSettings } from '@app/file-storage/shared/interfaces/main-settings.interface'
import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export default registerAs('main', (): IMainSettings => {
  const values: IMainSettings = {
    name: process.env.APP_NAME,
    description: process.env.APP_DESCRIPTION,
    version: process.env.APP_VERSION,
    port: parseInt(process.env.APP_PORT),
    aws: {
      region: process.env.AWS_DEFAULT_REGION,
      accountId: process.env.AWS_DEFAULT_ACCOUNT_ID
    }
  }
  const schema = Joi.object({
    name: Joi.string().required(),
    description: Joi.string().required(),
    version: Joi.string().required(),
    port: Joi.number().required(),
    aws: Joi.object({
      region: Joi.string().required(),
      accountId: Joi.string().required()
    }).required()
  }).required()
  const { error } = schema.validate(values, { abortEarly: false })
  if (error) {
    throw new Error(error.message)
  }
  return values
})
