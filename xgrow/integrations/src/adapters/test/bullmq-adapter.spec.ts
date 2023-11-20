import BullMqProvider from '../../providers/bullmq'
import BullMqAdapter from '../bullmq-adapter'

jest.mock('../../providers/bullmq',() => ({
  getInstance: jest.fn()
}))

describe('BullMqAdapter', () => {
  let bullMqInstance

  it('should return ', () => {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    bullMqInstance = new BullMqAdapter().instance()
    const spyOnProvider = jest.spyOn(BullMqProvider, 'getInstance')
    expect(spyOnProvider).toBeCalledTimes(1)
  })
})
