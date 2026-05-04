# Model Relationships

## Client Model
- hasMany: ClientContact
- hasMany: WorkOrder
- hasMany: Invoice
- hasMany: Payment
- hasMany: Receivable
- hasMany: StopLicense
- hasMany: DebitCreditNote

## Invoice Model
- belongsTo: InvoiceType
- belongsTo: Client
- belongsTo: Bank
- hasMany: InvoiceItem
- hasMany: Payment
- hasMany: PostingPaymentInvoice
- hasOne: Receivable
- hasOne: DebitCreditNote
- morphMany: Journal

... (untuk semua models)