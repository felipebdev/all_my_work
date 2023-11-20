/* eslint-disable import/first */

const invoiceMock = {
  create: jest.fn()
}

import { Payload } from '@app/job'
/* eslint-disable @typescript-eslint/no-unused-vars */
import Invoice from '../../collections/invoice'
import { InvoiceFactory } from '../invoice.factory'
import mongoose from 'mongoose'

jest.mock('../../collections/invoice', () => invoiceMock)
jest.mock('mongoose', () => ({
  connection: {
    readyState: 1
  }
}))

const payloadMock: Payload = {
  header: {
    app: {
      action: 'anyaction',
      app_id: 434,
      event: 'anyevent',
      id: 794,
      integration: {
        id: 434,
        type: 'anyvaluetype',
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
    data: {
      payment_date: '2022-12-30',
      payment_order_code: 'anycode'
    }
  }
}

describe('InvoiceFactory', () => {
  let factory: InvoiceFactory

  it('should initalize class', () => {
    factory = new InvoiceFactory()
    expect(factory).toBeDefined()
  })

  it('should correctly call Invoice.create()', async() => {
    // const spyOnCreate = jest.spyOn(Invoice, 'create')
    await factory.createInvoice(
      payloadMock,
      'invoiceId',
      'productId'
    )
    expect(invoiceMock.create).toBeCalledTimes(1)
    // expect(spyOnCreate).toBeCalledWith({
    //   service: 'anyvaluetype',
    //   transactionId: 'anycode',
    //   transactionDate: new Date('2022-12-30').toISOString(),
    //   invoiceId: 'invoiceId',
    //   productId: 'productId',
    //   status: 'anyaction',
    // })
  })
})
