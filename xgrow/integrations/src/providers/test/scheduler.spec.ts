/* eslint-disable import/first */
/* eslint-disable node/no-callback-literal */

const addSimpleIntervalJob = jest.fn()

const toadSchedulerMock = {
  ToadScheduler: jest.fn(() => ({
    addSimpleIntervalJob: jest.fn()
  })),
  SimpleIntervalJob: jest.fn(() => ({
    addSimpleIntervalJob
  })),
  Task: jest.fn((queueName, cb) => {
    // eslint-disable-next-line node/no-callback-literal
    cb()
  }),
}

/* eslint-disable @typescript-eslint/no-unused-vars */
import { ToadScheduler, SimpleIntervalJob, Task } from 'toad-scheduler'
import { SchedulerProvider, Scheduler } from '../scheduler'
import WinstonLog from '../winston'

jest.mock('../winston', () => ({
  getInstance: jest.fn().mockReturnValue({
    debug: jest.fn(),
  }),
}))

jest.mock('toad-scheduler', () => {
  return toadSchedulerMock
})

describe('Scheduler', () => {
  let scheduler: Scheduler

  beforeAll(() => {
    scheduler = SchedulerProvider.getInstance()
    jest.useFakeTimers()
  })

  beforeEach(() => {
    jest.clearAllMocks()
    jest.clearAllTimers()
  })

  describe('initHealthLogger', () => {
    it('should add a SimpleIntervalJob to the ToadScheduler', () => {
      scheduler.initHealthLogger()

      expect(toadSchedulerMock.SimpleIntervalJob).toBeCalledTimes(1)
      expect(toadSchedulerMock.Task).toBeCalledTimes(1)
    })

    it('should log the uptime every 10 seconds', () => {
      scheduler.initHealthLogger()

      jest.advanceTimersByTime(300000)
      expect(WinstonLog.getInstance().debug).toHaveBeenCalledWith('Health Logging', { uptime: expect.any(Number) })

      jest.advanceTimersByTime(300000)
      expect(WinstonLog.getInstance().debug).toHaveBeenCalledWith('Health Logging', { uptime: expect.any(Number) })
    })
  })
})
