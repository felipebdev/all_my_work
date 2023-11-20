import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export const externalServicesConfig = () =>
  registerAs('external-services', () => {
    const values = {
      nextcode: {
        baseUrl: process.env.NEXTCODE_BASE_URL,
        accessToken: process.env.NEXTCODE_ACCESS_TOKEN
      },
      bigId: {
        baseUrl: process.env.BIG_ID_BASE_URL,
        accessToken: process.env.BIG_ID_ACCESS_TOKEN
      },
      bigBoost: {
        baseUrl: process.env.BIG_BOOST_BASE_URL,
        user: process.env.BIG_BOOST_USER,
        password: process.env.BIG_BOOST_PASSWORD
      },
      serpro: {
        baseUrl: process.env.SERPRO_BASE_URL,
        accountKey: process.env.SERPRO_ACCOUNT_KEY,
        secretKey: process.env.SERPRO_SECRET_KEY,
        validation: process.env.SERPRO_VALIDATION
      },
      checkoutApi: {
        baseUrl: process.env.CHECKOUT_API_BASE_URL,
        jwtSecret: process.env.CHECKOUT_JWT_SECRET
      }
    }
    const schema = Joi.object({
      nextcode: Joi.object({
        baseUrl: Joi.string().required(),
        accessToken: Joi.string().required()
      }).required(),
      bigId: Joi.object({
        baseUrl: Joi.string().required(),
        accessToken: Joi.string().required()
      }).required(),
      bigBoost: Joi.object({
        baseUrl: Joi.string().required(),
        user: Joi.string().required(),
        password: Joi.string().required()
      }).required(),
      serpro: Joi.object({
        baseUrl: Joi.string().required(),
        accountKey: Joi.string().required(),
        secretKey: Joi.string().required(),
        validation: Joi.string().required()
      }).required(),
      checkoutApi: Joi.object({
        baseUrl: Joi.string().required(),
        jwtSecret: Joi.string().required()
      }).required()
    }).required()

    const { error } = schema.validate(values, { abortEarly: false })

    if (error) {
      throw new Error(error.message)
    }
    return values
  })
