import { Payload } from '@app/job'
import { ValidateJsAdapter } from '../validatejs-adapter'

describe('ValidateJsAdapter', () => {
  let validator: ValidateJsAdapter

  const validateSchema = {
    'header.app.integration.type': (value) =>
      !!value && ['anyvalue', 'anyvalue2'].includes(value),
    'header.app.event': (value) => !!value && value === 'anyevent'
  }

  const expectedTruthyPayload: Payload = {
    header: {
      app: {
        action: null,
        app_id: 434,
        event: 'anyevent',
        id: 794,
        integration: {
          id: 434,
          type: 'anyvalue',
        },
        planIds: [
          1,
          2
        ],
        platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
      },
      date: '2022-12-30 08:54:23'
    },
    payload: {
      data: {}
    }
  }

  const expectedFalsyPayload: Payload = {
    header: {
      app: {
        action: null,
        app_id: 434,
        event: 'anydifferent',
        id: 794,
        integration: {
          id: 434,
          type: 'octadesk',
        },
        planIds: [
          1,
          2
        ],
        platform_id: '89d6084b-99ae-481c-8646-05c99c98b469'
      },
      date: '2022-12-30 08:54:23'
    },
    payload: {
      data: {}
    }
  }

  beforeEach(() => {
    jest.clearAllTimers()
    jest.clearAllMocks()
  })

  it('should initialize class correctly', () => {
    validator = new ValidateJsAdapter()
    expect(validator).toBeDefined()
  })

  it('should return true if payload is valid after comparison', () => {
    const response = validator.validate(validateSchema, expectedTruthyPayload)
    expect(response).toBeTruthy()
  })

  it('should return false if payload is invalid after comparison', () => {
    const response = validator.validate(validateSchema, expectedFalsyPayload)
    expect(response).toBeFalsy()
  })
})
