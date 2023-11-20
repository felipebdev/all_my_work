import { Module } from '@nestjs/common'
import { TokenResolver } from '@app/token/resolvers/token.resolver'
import { TokenService } from '@app/token/services/token.service'

@Module({
  providers: [TokenResolver, TokenService],
  exports: [TokenService]
})
export class TokenModule {}
