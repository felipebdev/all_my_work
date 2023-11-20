import { Injectable } from '@nestjs/common'

@Injectable()
export class AbstractFactory {
  createTo<T, K>(data: T, type: { new (): K }): K {
    const result = new type()
    return Object.assign(result, data)
  }
}
