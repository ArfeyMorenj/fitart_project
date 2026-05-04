    <?php

    use App\Http\Controllers\Api\AuthController;
    use App\Http\Controllers\Api\BankController;
    use App\Http\Controllers\Api\ChartOfAccountController;
    use App\Http\Controllers\Api\ClientController;
    use App\Http\Controllers\Api\CompanyController;
    use App\Http\Controllers\Api\CompanySettingController;
    use App\Http\Controllers\Api\DebitCreditNoteController;
    use App\Http\Controllers\Api\InstallationController;
    use App\Http\Controllers\Api\InvoiceController;
    use App\Http\Controllers\Api\InvoiceItemController;
    use App\Http\Controllers\Api\InvoiceSeriesController;
    use App\Http\Controllers\Api\InvoiceTypeController;
    use App\Http\Controllers\Api\ItemController;
    use App\Http\Controllers\Api\JournalController;
    use App\Http\Controllers\Api\JpasReportController;
    use App\Http\Controllers\Api\LicenseController;
    use App\Http\Controllers\Api\MasterItemProductController;
    use App\Http\Controllers\Api\PaymentController;
    use App\Http\Controllers\Api\PostingOmzetController;
    use App\Http\Controllers\Api\PostingPaymentController;
    use App\Http\Controllers\Api\PostingPaymentCostController;
    use App\Http\Controllers\Api\PrintController;
    use App\Http\Controllers\Api\ProductController;
    use App\Http\Controllers\Api\ProductGroupController;
    use App\Http\Controllers\Api\ProtectionController;
    use App\Http\Controllers\Api\ReceivableController;
    use App\Http\Controllers\Api\ReceivableReportController;
    use App\Http\Controllers\Api\RecurringInvoiceController;
    use App\Http\Controllers\Api\ReportController;
    use App\Http\Controllers\Api\SalesReportController;
    use App\Http\Controllers\Api\StopLicenseController;
    use App\Http\Controllers\Api\TaxReportController;
    use App\Http\Controllers\Api\TeamMemberController;
    use App\Http\Controllers\Api\UserController;
    use App\Http\Controllers\Api\WorkOrderController;
    use Illuminate\Support\Facades\Route;

    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        // Setup
        Route::get('/company/settings', [CompanySettingController::class, 'show']);
        Route::post('/company/settings', [CompanySettingController::class, 'update'])
            ->middleware('role:super_admin,finance_admin');

        Route::get('/tax-series', [InvoiceSeriesController::class, 'index'])
            ->middleware('role:super_admin,finance_admin,manager');
        Route::post('/tax-series', [InvoiceSeriesController::class, 'store'])
            ->middleware('role:super_admin,finance_admin');
        Route::post('/tax-series/{taxSeries}/next', [InvoiceSeriesController::class, 'nextNumber'])
            ->middleware('role:super_admin,finance_admin');
        Route::delete('/tax-series/{taxSeries}', [InvoiceSeriesController::class, 'destroy'])
            ->middleware('role:super_admin,finance_admin');

        // Lookups
        Route::get('/companies/search', [CompanyController::class, 'search']);
        Route::get('/clients/search', [ClientController::class, 'search']);
        Route::get('/products/search', [ProductController::class, 'search']);
        Route::get('/items/search', [ItemController::class, 'search']);
        Route::get('/banks/search', [BankController::class, 'search']);
        Route::get('/team-members/search', [TeamMemberController::class, 'search']);
        Route::get('/invoice-types/search', [InvoiceTypeController::class, 'search']);
        Route::get('/master-item-products/search', [MasterItemProductController::class, 'search']);
        Route::get('/product-groups/search', [ProductGroupController::class, 'search']);
        Route::get('/chart-of-accounts/search', [ChartOfAccountController::class, 'search']);
        Route::get('/chart-of-accounts', [ChartOfAccountController::class, 'index']);
        Route::get('/chart-of-accounts/{chartOfAccount}', [ChartOfAccountController::class, 'show']);

        // Master data
        Route::apiResource('users', UserController::class)
            ->middleware('role:super_admin');
        Route::post('users/{user}/assign-role', [UserController::class, 'assignRole'])
            ->middleware('role:super_admin');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
            ->middleware('role:super_admin');

        Route::apiResource('companies', CompanyController::class)
            ->middleware('role:super_admin');
        Route::apiResource('clients', ClientController::class)
            ->middleware('role:super_admin,sales_operator,manager,finance_admin,ar_collector');
        Route::apiResource('team-members', TeamMemberController::class)
            ->middleware('role:super_admin,sales_operator');
        Route::apiResource('product-groups', ProductGroupController::class)
            ->middleware('role:super_admin,sales_operator');
        Route::apiResource('products', ProductController::class)
            ->middleware('role:super_admin,sales_operator');
        Route::apiResource('items', ItemController::class)
            ->middleware('role:super_admin,sales_operator');
        Route::apiResource('banks', BankController::class)
            ->middleware('role:super_admin,finance_admin');
        Route::apiResource('invoice-types', InvoiceTypeController::class)
            ->middleware('role:super_admin,finance_admin');
        Route::post('invoice-types/{invoiceType}/toggle-status', [InvoiceTypeController::class, 'toggleStatus'])
            ->middleware('role:super_admin,finance_admin');
        Route::apiResource('master-item-products', MasterItemProductController::class)
            ->middleware('role:super_admin,finance_admin,sales_operator');
        Route::post('master-item-products/{masterItemProduct}/toggle-status', [MasterItemProductController::class, 'toggleStatus'])
            ->middleware('role:super_admin,finance_admin');

        // Sales
        Route::get('work-orders/search', [WorkOrderController::class, 'search'])
            ->middleware('role:super_admin,sales_operator,manager');
        Route::apiResource('work-orders', WorkOrderController::class)
            ->middleware('role:super_admin,sales_operator,manager');
        Route::post('work-orders/{work_order}/assign-team', [WorkOrderController::class, 'assignTeam'])
            ->middleware('role:super_admin,sales_operator');

        Route::apiResource('installations', InstallationController::class)
            ->middleware('role:super_admin,sales_operator');
        Route::apiResource('licenses', LicenseController::class)
            ->middleware('role:super_admin,sales_operator');

        Route::get('stop-licenses/search', [StopLicenseController::class, 'search'])
            ->middleware('role:super_admin,sales_operator');
        Route::apiResource('stop-licenses', StopLicenseController::class)
            ->middleware('role:super_admin,sales_operator');

        // Finance read
        Route::get('invoice-license', [InvoiceController::class, 'licenseIndex'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::get('invoice-license/search', [InvoiceController::class, 'licenseSearch'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::get('invoice-license/{invoice}/summary-journal', [InvoiceController::class, 'licenseSummaryJournal'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::get('invoice-license/{invoice}', [InvoiceController::class, 'licenseShow'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');

        Route::get('invoice-products', [InvoiceController::class, 'productIndex'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::get('invoice-products/search', [InvoiceController::class, 'productSearch'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::get('invoice-products/{invoice}/summary-journal', [InvoiceController::class, 'productSummaryJournal'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::get('invoice-products/{invoice}', [InvoiceController::class, 'productShow'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');

        Route::apiResource('invoices', InvoiceController::class)
            ->only(['index', 'show'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::apiResource('invoice-items', InvoiceItemController::class)
            ->only(['index', 'show'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::apiResource('payments', PaymentController::class)
            ->only(['index', 'show'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');

        Route::get('debit-credit-notes/search', [DebitCreditNoteController::class, 'search'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::get('debit-credit-notes/{debitCreditNote}/summary-journal', [DebitCreditNoteController::class, 'summaryJournal'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');
        Route::apiResource('debit-credit-notes', DebitCreditNoteController::class)
            ->only(['index', 'show'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');

        Route::apiResource('receivables', ReceivableController::class)
            ->only(['index', 'show'])
            ->middleware('role:super_admin,finance_admin,manager,ar_collector');

        // Posting read
        Route::prefix('posting')->middleware('role:super_admin,finance_admin,manager,ar_collector')->group(function () {
            Route::get('payments', [PostingPaymentController::class, 'index']);
            Route::get('payments/search', [PostingPaymentController::class, 'search']);
            Route::get('payments/{postingPayment}', [PostingPaymentController::class, 'show']);
            Route::get('payments/{postingPayment}/summary-journal', [PostingPaymentController::class, 'summaryJournal']);
            Route::get('payments/{postingPayment}/costs', [PostingPaymentCostController::class, 'index']);
        });

        Route::get('journals', [JournalController::class, 'index'])
            ->middleware('role:super_admin,finance_admin,manager');

        // Reports
        Route::prefix('reports')->middleware('role:super_admin,finance_admin,manager,ar_collector')->group(function () {
            Route::get('dashboard', [ReportController::class, 'dashboard']);
            Route::get('ar/summary', [ReportController::class, 'arSummary']);
            Route::get('ar/ledger', [ReportController::class, 'arLedger']);

            Route::get('invoices/register', [ReportController::class, 'invoiceRegister']);
            Route::get('invoices/history', [JpasReportController::class, 'invoiceHistory']);

            Route::get('tax', [ReportController::class, 'taxReport']);
            Route::get('tax/vat', [TaxReportController::class, 'vat']);
            Route::get('tax/summary', [TaxReportController::class, 'summary']);

            Route::get('sales', [ReportController::class, 'salesReport']);
            Route::get('sales/summary', [SalesReportController::class, 'summary']);
            Route::get('sales/client', [SalesReportController::class, 'perClient']);

            Route::get('cash', [ReportController::class, 'cashReport']);
            Route::get('fiscal-commercial', [JpasReportController::class, 'fiscalCommercial']);

            Route::get('receivable/by-item', [JpasReportController::class, 'receivableByItem']);
            Route::get('receivable/data', [ReceivableReportController::class, 'data']);
            Route::get('receivable/process-detail', [ReceivableReportController::class, 'processDetail']);
            Route::get('receivable/summary', [ReceivableReportController::class, 'summary']);
            Route::get('receivable/detail', [ReceivableReportController::class, 'detail']);
        });

        // Print
        Route::prefix('print')->middleware('role:super_admin,finance_admin,manager,ar_collector')->group(function () {
            Route::get('invoices/batch', [PrintController::class, 'batchInvoices']);
            Route::post('invoices/batch', [PrintController::class, 'queueBatchInvoices']);
            Route::get('invoices/{invoice}/pdf', [PrintController::class, 'invoicePdf']);
            Route::get('receipts/{posting_payment}/pdf', [PrintController::class, 'receiptPdf']);
        });

        // Protection
        Route::apiResource('protections', ProtectionController::class)
            ->middleware('role:super_admin');

        // Write routes protected by accounting period
        Route::middleware('period.unlocked')->group(function () {
            Route::apiResource('invoices', InvoiceController::class)
                ->only(['store', 'update', 'destroy'])
                ->middleware('role:super_admin,finance_admin');
            Route::apiResource('invoice-items', InvoiceItemController::class)
                ->only(['store', 'update', 'destroy'])
                ->middleware('role:super_admin,finance_admin');
            Route::apiResource('payments', PaymentController::class)
                ->only(['store', 'update', 'destroy'])
                ->middleware('role:super_admin,finance_admin,ar_collector');
            Route::apiResource('debit-credit-notes', DebitCreditNoteController::class)
                ->only(['store', 'update', 'destroy'])
                ->middleware('role:super_admin,finance_admin');

            Route::post('receivables/process', [ReceivableController::class, 'process'])
                ->middleware('role:super_admin,finance_admin,ar_collector');
            Route::post('recurring/invoices/generate', [RecurringInvoiceController::class, 'generate'])
                ->middleware('role:super_admin,finance_admin');

            Route::prefix('posting')->group(function () {
                Route::post('omzet', [PostingOmzetController::class, 'postInvoiceRevenue'])
                    ->middleware('role:super_admin,finance_admin');
                Route::post('payments', [PostingPaymentController::class, 'postBatch'])
                    ->middleware('role:super_admin,finance_admin,ar_collector');
                Route::post('payments/{postingPayment}/costs', [PostingPaymentCostController::class, 'store'])
                    ->middleware('role:super_admin,finance_admin,ar_collector');
                Route::put('payments/{postingPayment}/costs/{cost}', [PostingPaymentCostController::class, 'update'])
                    ->middleware('role:super_admin,finance_admin,ar_collector');
                Route::delete('payments/{postingPayment}/costs/{cost}', [PostingPaymentCostController::class, 'destroy'])
                    ->middleware('role:super_admin,finance_admin,ar_collector');
            });
        });
    });
