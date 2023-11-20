import { Module } from '@nestjs/common'
import { LeadResolver } from '@app/lead/resolvers/lead.resolver'
import { LeadService } from '@app/lead/services/lead.service'

@Module({
  providers: [LeadResolver, LeadService]
})
export class LeadModule {}
