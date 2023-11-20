import { ParametrizationService } from '@app/parametrization/services/parametrization.service'
import { Module } from '@nestjs/common'

@Module({
  providers: [ParametrizationService],
  exports: [ParametrizationService]
})
export class ParametrizationModule {}
