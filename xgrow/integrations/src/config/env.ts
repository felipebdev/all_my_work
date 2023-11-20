import { bool } from '../utils/helper'
import dotenv from 'dotenv'
import joi from 'joi'

dotenv.config()

const env = {
  environment: process.env.APP_ENVIRONMENT,
  redis: {
    port: Number(process.env.REDIS_PORT),
    host: process.env.REDIS_HOST,
    username: process.env.REDIS_USERNAME,
    password: process.env.REDIS_PASSWORD,
    db: Number(process.env.REDIS_DB),
    enableTLSForSentinelMode: bool(process.env.REDIS_ENABLE_TLS),
    tls: (bool(process.env.REDIS_ENABLE_TLS))
      ? {
          rejectUnauthorized: (bool(process.env.REDIS_TLS_REJECT_UNAUTHORIZED))
        }
      : undefined
  },
  queue: {
    name: process.env.QUEUE_NAME,
  },
  mongo: {
    srv: process.env.MONGO_SRV,
    db: process.env.MONGO_DB_NAME,
    ssl: bool(process.env.MONGO_SSL),
    sslCaPath: process.env.MONGO_SSL_CA_PATH
  },
  elastic: {
    apm: {
      secretToken: process.env.ELASTIC_APM_SECRET_TOKEN,
      serverUrl: process.env.ELASTIC_APM_SERVER_URL
    }
  }
}

const envVarsSchema = joi.object().keys({
  environment: joi.string().valid('test','dev', 'development', 'prod', 'production', 'hml', 'homolog', 'stg', 'staging').required(),
  redis: joi.object().keys({
    port: joi.number().required(),
    host: joi.string().required(),
    username: joi.string().required(),
    password: joi.string().required(),
    db: joi.number().required(),
    enableTLSForSentinelMode: joi.boolean().required(),
    tls: joi.object().keys({
      rejectUnauthorized: joi.boolean().required()
    }).optional()
  }).required(),
  queue: joi.object().keys({
    name: joi.string().required(),
  }).required(),
  mongo: joi.object().keys({
    srv: joi.string().required(),
    db: joi.string().required(),
    ssl: joi.boolean().required(),
    sslCaPath: joi.string().required()
  }).required(),
  elastic: joi.object().keys({
    apm: joi.object().keys({
      secretToken: joi.string().required(),
      serverUrl: joi.string().required()
    }).required()
  }).required()
}).required()

export const validateEnvs = (): void => {
  const { error } = envVarsSchema
    .prefs({ errors: { label: 'key' } })
    .validate(env)

  if (error) {
    throw new Error(`Config validation error: ${error.message}`)
  }
}

export default env
