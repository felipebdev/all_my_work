import { JobResponse } from '@app/job'
import { LogLevel } from '@app/providers/winston'

export default interface ILogable {
  debug: (message: string, payload?: any) => void
  info: (message: string, payload?: any) => void
  warning: (message: string, payload?: any) => void
  error: (message: string, payload?: any) => void
  emergency: (message: string, payload?: any) => void
  logJobResponse: (level: LogLevel, message: string, payload?: JobResponse, extra?: any) => void
}
