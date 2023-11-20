import { Inject } from '@nestjs/common'
import { SqsService } from '@nestjs-packages/sqs'

export function SqsProducer(queue: string) {
  const injectSqs = Inject(SqsService)
  return function (target: any, key: string, descriptor: PropertyDescriptor) {
    injectSqs(target, 'sqsService')
    const originalMethod = descriptor.value
    descriptor.value = async function (...args) {
      try {
        const methodReturn = await originalMethod.apply(this, args)
        const sqsService: SqsService = this.sqsService
        await sqsService.send(queue, {
          body: methodReturn,
          id: '1234'
        })
        return methodReturn
      } catch (error) {
        console.error(error)
        throw new Error('Somenthing went wrong.')
      }
    }
  }
}
