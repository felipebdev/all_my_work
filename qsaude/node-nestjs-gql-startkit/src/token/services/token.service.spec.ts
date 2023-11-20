import { createMock } from '@golevelup/nestjs-testing'
import { HttpService } from '@nestjs/axios'
import { ConfigModule, ConfigService } from '@nestjs/config'
import { Test, TestingModule } from '@nestjs/testing'
import { TokenService } from './token.service'

describe('TokenService', () => {
  let service: TokenService

  const mockConfigService = createMock<ConfigService>({
    get: jest.fn(() => 'any-url')
  })

  beforeEach(async () => {
    const module: TestingModule = await Test.createTestingModule({
      imports: [ConfigModule.forRoot()],
      providers: [
        TokenService,
        {
          provide: HttpService,
          useValue: {
            axiosRef: {
              get: jest.fn(() => ({ data: {} })),
              post: jest.fn(() => ({ data: {} }))
            }
          }
        }
      ]
    })
      .overrideProvider(ConfigService)
      .useValue(mockConfigService)
      .compile()

    service = module.get<TokenService>(TokenService)
  })

  it('should be defined', () => {
    expect(service).toBeDefined()
  })
})
