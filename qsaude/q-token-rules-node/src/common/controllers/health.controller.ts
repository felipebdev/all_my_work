import { HealthService } from '@app/common/services'
import { Controller, Get } from '@nestjs/common'
import { HealthCheck, HealthCheckResult } from '@nestjs/terminus'

@Controller('health')
export class HealthController {
  constructor(private healthService: HealthService) {}
  @Get()
  @HealthCheck()
  async getHandle(): Promise<HealthCheckResult> {
    return this.healthService.performHealthCheck()
  }
}
