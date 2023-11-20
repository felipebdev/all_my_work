import fs from 'fs'
import { connect } from 'mongoose'
import env from '../../config/env'

const sslCertificate = (
  env.mongo.ssl && env.mongo.sslCaPath
    ? [fs.readFileSync(env.mongo.sslCaPath)]
    : undefined
)

export default class Mongodb {
  async connect (): Promise<void> {
    const options = {
      autoIndex: false,
      useNewUrlParser: true,
      useUnifiedTopology: true,
      sslValidate: env.mongo.ssl
    }

    if (sslCertificate) options['sslCA'] = sslCertificate
    await connect(env.mongo.srv, options)
  }
}
