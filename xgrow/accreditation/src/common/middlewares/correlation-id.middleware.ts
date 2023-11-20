import { Injectable, NestMiddleware } from '@nestjs/common'
import { NextFunction, Request, Response } from 'express'
import { v4 as uuidv4 } from 'uuid'

@Injectable()
export class CorrelationIdMiddleware implements NestMiddleware {
  use(req: Request, res: Response, next: NextFunction) {
    const CORRELATION_ID_KEY = 'x-correlation-id'
    let correlationId = req.headers[CORRELATION_ID_KEY]
    if (!correlationId) {
      correlationId = uuidv4()
    }
    req.headers[CORRELATION_ID_KEY] = correlationId
    res.append(CORRELATION_ID_KEY, correlationId)
    next()
  }
}
