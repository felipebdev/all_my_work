import { IApplicationsSettings } from '@app/file-storage/shared/interfaces/applications-settings.interface'
import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export default registerAs('applications', (): IApplicationsSettings => {
  const values: IApplicationsSettings = {
    portalEmpresas: {
      aws: {
        s3: {
          bucket: process.env.PORTAL_EMPRESAS_BUCKET
        }
      },
      storage: {
        path: process.env.PORTAL_EMPRESAS_STORAGE
      },
      notification: {
        importCsvTransactions: process.env.PORTAL_EMPRESAS_IMPORT_CSV_TRANSACTION_TOPIC
      }
    }
  }
  const schema = Joi.object({
    portalEmpresas: Joi.object({
      aws: Joi.object({
        s3: Joi.object({
          bucket: Joi.string().required()
        }).required()
      }).required(),
      storage: Joi.object({
        path: Joi.string().required()
      }).required(),
      notification: Joi.object({
        importCsvTransactions: Joi.string().required()
      }).required()
    }).required()
  })
  const { error } = schema.validate(values, { abortEarly: false })
  if (error) {
    throw new Error(error.message)
  }
  return values
})
