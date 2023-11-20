import mongoose from 'mongoose'
import Invoice from '../collections/invoice'
import { Payload } from '../../../job'

export class InvoiceFactory {
  public async createInvoice(
    payload: Payload,
    invoiceId: string,
    productId: string,
  ): Promise<void> {
    if (mongoose.connection.readyState === 1) {
      await Invoice.create({
        service: payload.header.app.integration.type,
        transactionId: payload.payload.data.payment_order_code,
        transactionDate: new Date(payload.payload.data.payment_date).toISOString(),
        invoiceId,
        productId,
        status: payload.header.app.action,
      })
    }
  }
}
