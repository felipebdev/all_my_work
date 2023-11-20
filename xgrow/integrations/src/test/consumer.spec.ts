import IConsumable from '../contracts/consumable'
import { Job } from '../job'
import Consumer from '../consumer'
import { Queue } from 'bullmq'

const mockConsumable: IConsumable = {
  consume: jest.fn(),
  queue: {} as unknown as Queue,
}

describe('Consumer', () => {
  it('should call the consume method on the given instance', () => {
    const spy = jest.spyOn(mockConsumable, 'consume')

    Consumer.init(mockConsumable)

    expect(spy).toHaveBeenCalledWith(Job.process, Job.resolve, Job.reject)
  })
})
