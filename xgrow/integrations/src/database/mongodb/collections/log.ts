import { Schema, model } from 'mongoose'
import timeZone from 'mongoose-timezone'

export enum LogStatus {
  SUCCESS = 'success',
  FAILED = 'failed'
}

const LogSchema = new Schema(
  {
    jobId: Schema.Types.String,
    service: Schema.Types.String,
    status: {
      type: Schema.Types.String,
      enum: LogStatus
    },
    metadata: {
      action_id: Schema.Types.String,
      app_id: Schema.Types.String,
      platform_id: Schema.Types.String,
      event: Schema.Types.String
    },
    request: {
      url: Schema.Types.String,
      method: Schema.Types.String,
      key: Schema.Types.String,
      headers: {},
      payload: {}
    },
    response: {
      code: Schema.Types.Number,
      message: Schema.Types.String,
      payload: {}
    }
  },
  {
    timestamps: true
  }
)

LogSchema.plugin(timeZone, { paths: ['createdAt', 'updatedAt'] })
const Log = model('Log', LogSchema)
export default Log
