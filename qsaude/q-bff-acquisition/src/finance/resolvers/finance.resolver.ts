import { Finance, FinanceInput, UpdateFinanceInput } from '@app/finance/models'
import { FinanceService } from '@app/finance/services'
import { Args, Mutation, Query, Resolver } from '@nestjs/graphql'

@Resolver()
export class FinanceResolver {
  constructor(private readonly financeService: FinanceService) {}

  @Query(() => Finance, { nullable: false })
  finance(@Args('idProposal', { type: () => String }) id: string): Promise<Finance> {
    return this.financeService.getByProposal(id)
  }

  @Mutation(() => Finance, { nullable: false })
  createFinance(@Args('finance', { type: () => FinanceInput }) finance: FinanceInput): Promise<Finance> {
    return this.financeService.create(finance)
  }

  @Mutation(() => Finance, { nullable: false })
  updateFinance(
    @Args('id', { type: () => String }) id: string,
    @Args('finance', { type: () => UpdateFinanceInput }) finance: UpdateFinanceInput
  ): Promise<Finance> {
    return this.financeService.update(id, finance)
  }
}
