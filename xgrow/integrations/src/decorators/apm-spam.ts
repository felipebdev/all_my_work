import apm from 'elastic-apm-node'

export function ApmSpan(name: string) {
  return function (target: any, key: string, descriptor: PropertyDescriptor): any {
    const originalMethod = descriptor.value
    descriptor.value = async function (...args) {
      const span = apm.startSpan(name)
      const originalMethodReturn = await originalMethod.apply(this, args)
      await span.end()
      return originalMethodReturn
    }
  }
}
