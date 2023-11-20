import { TokenService } from '@app/token/services/token.service'
import { Test, TestingModule } from '@nestjs/testing'
import { TokenResolver } from './token.resolver'

describe('TokenResolver', () => {
  let resolver: TokenResolver

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      providers: [
        TokenResolver,
        {
          provide: TokenService,
          useValue: {
            create: jest.fn(() => ({}))
          }
        }
      ]
    }).compile()

    resolver = module.get<TokenResolver>(TokenResolver)
  })

  it('should be defined', () => {
    expect(resolver).toBeDefined()
  })
})
