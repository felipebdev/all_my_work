import apm from 'elastic-apm-node'

jest.mock('elastic-apm-node', () => ({
    startTransaction: jest.fn(() => ({
      result: null,
      end: jest.fn()
    })),
    startSpan: jest.fn(() => ({
      result: null,
      end: jest.fn()
    })),
    captureError: jest.fn()
}))
  