import { Schema, model } from 'mongoose'
import timeZone from 'mongoose-timezone'

const InvoiceSchema = new Schema({
  service: Schema.Types.String,
  transactionId: {
    type: Schema.Types.String,
    require: true,
    unique: true
  },
  transactionDate: Schema.Types.String,
  invoiceId: {
    type: Schema.Types.String,
    require: true,
    unique: true
  },
  productId: Schema.Types.String,
  status: Schema.Types.String,
})

InvoiceSchema.plugin(timeZone, { paths: ['createdAt', 'updatedAt'] })
const Invoice = model('Invoice', InvoiceSchema)
export default Invoice
