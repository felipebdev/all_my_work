import { registerAs } from '@nestjs/config'
import * as Joi from 'joi'

export const proposalConfig = () =>
  registerAs('proposal', () => {
    const values = {
      initialProposalNumber: process.env.INITIAL_PROPOSAL_NUMBER
    }
    const schema = Joi.object({
      initialProposalNumber: Joi.number().required()
    }).required()
    const { error } = schema.validate(values, { abortEarly: false })
    if (error) {
      throw new Error(error.message)
    }
    return values
  })
