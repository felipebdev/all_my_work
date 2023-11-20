import { JobResponse } from '@app/job'
import ILogable from '../contracts/logable'
import winston, { createLogger, format, transports } from 'winston'
import { v4 as uuid } from 'uuid'

export type LogLevel = 'DEBUG' | 'INFO' | 'WARNING' | 'ERROR' | 'EMERGENCY'

export enum LogLevelCode {
  DEBUG = 100,
  INFO = 200,
  WARNING = 300,
  ERROR = 400,
  EMERGENCY = 600,
}

interface Log {
  context: {
    event: string
    platform_id: string
    plans_id: number[]
    integration: string
    subscriber_id: number
    response_status: number
    action: string
    total_actions: number
    success: boolean
    correlation_id: string
    isRetry: boolean
    jobId: string
    platform_name?: string
    URL?: string
    error?: Error
  }
  level_name: LogLevel
  channel: string
  datetime: string
  extra: any
}

class Winston implements ILogable {
  private static readonly TIMESTAMP_FORMAT = 'YYYY-MM-DD HH:mm:ss'
  private readonly logger: winston.Logger
  private readonly customLevels = {
    levels: {
      emergency: 0,
      error: 1,
      warning: 2,
      info: 3,
      debug: 4
    }
  }

  constructor () {
    this.logger = createLogger({
      levels: this.customLevels.levels,
      transports: [
        new transports.Console({
          level: 'debug',
          format: format.printf((info) => {
            return `${JSON.stringify({
             ...info, level: LogLevelCode[info.level.toLocaleUpperCase()]
            })}`
          })
        })
      ]
    })
  }

  public logJobResponse(level: LogLevel, message: string, jobResponse: JobResponse, extra?: any): void {
    const { payload: { header: { app, correlation_id }, payload: { data } }, response, error, isRetry, jobId } = jobResponse
    const logData: Log = {
      context: {
        event: app.event,
        platform_id: app.platform_id,
        platform_name: app.platform_name,
        plans_id: app.planIds,
        integration: app.integration.type,
        subscriber_id: data.subscriber_id || null,
        response_status: response.status,
        action: app.action,
        total_actions: 1,
        success: ['DEBUG', 'INFO'].includes(level),
        error,
        correlation_id: correlation_id ?? uuid(),
        jobId,
        URL: response?.config?.url,
        isRetry
      },
      level_name: level,
      channel: process.env.APP_ENVIRONMENT,
      datetime: new Date().toISOString(),
      extra
    }
    this[level.toLocaleLowerCase()](message, logData)
  }

  public debug (message: string, payload?: any): void {
    this.logger.log('debug', message, payload)
  }

  public info(message: string, payload?: any): void {
    this.logger.log('info', message, payload)
  }

  public warning (message: string, payload?: any): void {
    this.logger.log('warning', message, payload)
  }

  public error (message: string, payload?: any): void {
    this.logger.log('error', message, payload)
  }

  public emergency (message: string, payload?: any): void {
    this.logger.log('emergency', message, payload)
  }
}

export default abstract class WinstonLog {
  static instance: Winston
  static getInstance (): Winston {
    if (!WinstonLog.instance) {
      WinstonLog.instance = new Winston()
    }

    return WinstonLog.instance
  }
}
