import { Lead } from '@app/lead/models/lead.model'
import { Args, Resolver, Query } from '@nestjs/graphql'
import { LeadService } from '../services/lead.service'

@Resolver()
export class LeadResolver {
  constructor(private readonly leadService: LeadService) {}
  @Query(() => Lead, { nullable: false })
  async lead(@Args('id', { type: () => String }) id: string): Promise<Lead> {
    return this.leadService.getLeadById(id) as unknown as Lead
  }
}
