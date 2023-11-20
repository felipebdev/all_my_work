import { Injectable } from '@nestjs/common'
import { HealthCheckResult, HealthCheckService, HttpHealthIndicator } from '@nestjs/terminus'
import { HealthService as IHealthService } from '@app/common/interfaces'

@Injectable()
export class HealthService implements IHealthService {
  constructor(private health: HealthCheckService, private http: HttpHealthIndicator) {}

  async performHealthCheck(): Promise<HealthCheckResult> {
    /* istanbul ignore next */
    return this.health.check([() => this.http.pingCheck('accreditation', 'https://docs.nestjs.com')])
  }
}
