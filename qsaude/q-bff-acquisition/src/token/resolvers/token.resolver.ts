import { CreateTokenInput } from '@app/token/models/token.input.model'
import { Token } from '@app/token/models/token.model'
import { TokenService } from '@app/token/services/token.service'
import { Args, Mutation, Resolver } from '@nestjs/graphql'

@Resolver()
export class TokenResolver {
  constructor(private readonly tokenService: TokenService) {}

  @Mutation(() => Token, { nullable: false })
  async createToken(@Args({ name: 'token', type: () => CreateTokenInput }) token: CreateTokenInput) {
    return this.tokenService.create(token)
  }
}
