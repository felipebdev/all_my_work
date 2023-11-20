import { healthConfig } from '../config';

export class HealthService {
  private readonly SOMEPROPERTY: string;
  private readonly SOMESECRETPROPERTY: string;

  constructor() {
    this.SOMEPROPERTY = healthConfig.someValue;
    this.SOMESECRETPROPERTY = healthConfig.someSecret;
  }

  getHealth(): string {
    return 'service is up';
  }
}
