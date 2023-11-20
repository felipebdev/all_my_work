/* eslint-disable @typescript-eslint/no-unused-vars */
import { HealthService } from '../service';

export class HealthController {
  private readonly healthService: HealthService;

  constructor() {
    this.healthService = new HealthService();
  }

  getHealth(event: any): string {
    return this.healthService.getHealth();
  }
}
