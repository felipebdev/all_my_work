type CheckoutActingAs = 'client'

export type CheckoutJwtPayload = {
  exp?: number
  platform_id: string
  user_id: string
  acting_as: CheckoutActingAs
}

export enum CheckoutRoutes {
  CREATE_RECIPIENT = '/recipients'
}

export type CheckoutHeaders = {
  Accept: string
  'Content-Type': string
  Authorization: string
  'X-Correlation-Id': string
}
