import { ToadScheduler, SimpleIntervalJob, Task } from 'toad-scheduler'
import WinstonLog from './winston'
import ILogable from '@app/contracts/logable'

export class Scheduler {
  private readonly scheduler: ToadScheduler = new ToadScheduler()
  private static readonly logger: ILogable = WinstonLog.getInstance()

  initHealthLogger(): void {
    const task = new Task('health-logger', () => {
      const uptime = process.uptime()
      Scheduler.logger.debug('Health Logging', {
        uptime
      })
    })

    const job = new SimpleIntervalJob({
      minutes: 30,
      runImmediately: true
    },
    task,
    {
      id: 'health-logger',
      preventOverrun: true
    })

    this.scheduler.addSimpleIntervalJob(job)
  }
}

export class SchedulerProvider {
  static instance: Scheduler
  static getInstance (): Scheduler {
    if (!SchedulerProvider.instance) {
      SchedulerProvider.instance = new Scheduler()
    }

    return SchedulerProvider.instance
  }
}
