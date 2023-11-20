import { JwtPayload } from '@app/auth/interfaces'
import { JwtStrategy } from '@app/auth/strategies'
import { ConfigService } from '@nestjs/config'
import { mock } from 'jest-mock-extended'

describe('JwtStrategy', () => {
  let jwtStrategy: JwtStrategy

  beforeEach(async () => {
    jwtStrategy = new JwtStrategy(
      mock<ConfigService>({
        get: jest.fn().mockReturnValue('dummy')
      })
    )
  })

  it('should be defined', () => {
    expect(jwtStrategy).toBeDefined()
  })
  describe('validate', () => {
    it('should return payload', async () => {
      const payload = mock<JwtPayload>()
      const result = await jwtStrategy.validate(payload)
      expect(result).toEqual(true)
    })
  })
})
