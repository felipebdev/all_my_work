import { registerAs } from '@nestjs/config';
import * as Joi from 'joi';

export const dbConfig = () =>
  registerAs('db', () => {
    const values = {
      uri: process.env.DB_URL,
    };

    const schema = Joi.object({
      uri: Joi.string().required(),
    });

    const { error } = schema.validate(values, { abortEarly: false });

    if (error) {
      throw new Error(`Joi validation error: ${error.message}`);
    }

    return values;
  });
