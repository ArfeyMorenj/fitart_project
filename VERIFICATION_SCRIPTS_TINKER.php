<?php
/**
 * Script untuk verifikasi data di database saat testing
 * Usage: php artisan tinker
 * 
 * Copy-paste commands dibawah ini untuk check data
 */

// ========== USERS ==========
echo "=== USER DATA ===\n";
User::select('id', 'username', 'name', 'email', 'role', 'is_active')->get();

// ========== TEAM MEMBERS ==========
echo "\n=== TEAM MEMBERS DATA ===\n";
TeamMember::select('id', 'code', 'name', 'position', 'status', 'is_active')->get();

// Count team members
echo "\nTotal Team Members: " . TeamMember::count();

// ========== PRODUCT GROUPS ==========
echo "\n\n=== PRODUCT GROUPS DATA ===\n";
ProductGroup::select('id', 'code', 'name', 'acc_omzet', 'cdf_piutang', 'is_active')->get();

// Count product groups
echo "\nTotal Product Groups: " . ProductGroup::count();

// Get product group with related products
echo "\n=== PRODUCT GROUP WITH PRODUCTS ===\n";
ProductGroup::with('products')->get();

// ========== PRODUCTS ==========
echo "\n\n=== PRODUCTS DATA ===\n";
Product::select('id', 'code', 'name', 'specification', 'product_group_id', 'is_active')->get();

// Count products
echo "\nTotal Products: " . Product::count();

// Get products with their group
echo "\n=== PRODUCTS WITH GROUP ===\n";
Product::with('productGroup')->get();

// ========== CHART OF ACCOUNTS ==========
echo "\n\n=== CHART OF ACCOUNTS DATA ===\n";
ChartOfAccount::select('id', 'code', 'name', 'is_active')->get();

// Count chart of accounts
echo "\nTotal Accounts: " . ChartOfAccount::count();

// ========== INVOICES ==========
echo "\n\n=== INVOICES DATA ===\n";
Invoice::select('id', 'invoice_number', 'date', 'customer_name', 'status', 'amount')->get();

// Count invoices
echo "\nTotal Invoices: " . Invoice::count();

// Get invoices with items
echo "\n=== INVOICES WITH ITEMS ===\n";
Invoice::with('invoiceItems.product')->get();

// ========== INVOICE ITEMS ==========
echo "\n\n=== INVOICE ITEMS DATA ===\n";
InvoiceItem::select('id', 'invoice_id', 'product_id', 'quantity', 'unit_price', 'amount')->get();

// Count invoice items
echo "\nTotal Invoice Items: " . InvoiceItem::count();

// ========== JOURNAL ENTRIES ==========
echo "\n\n=== JOURNAL ENTRIES DATA ===\n";
JournalEntry::select('id', 'entry_number', 'date', 'description', 'status')->get();

// Count journal entries
echo "\nTotal Journal Entries: " . JournalEntry::count();

// Get journal entries with details
echo "\n=== JOURNAL ENTRIES WITH DETAILS ===\n";
JournalEntry::with('details')->get();

// ========== DATA COUNT SUMMARY ==========
echo "\n\n=== DATA SUMMARY ===\n";
echo "Users: " . User::count() . "\n";
echo "Team Members: " . TeamMember::count() . "\n";
echo "Product Groups: " . ProductGroup::count() . "\n";
echo "Products: " . Product::count() . "\n";
echo "Chart of Accounts: " . ChartOfAccount::count() . "\n";
echo "Invoices: " . Invoice::count() . "\n";
echo "Invoice Items: " . InvoiceItem::count() . "\n";
echo "Journal Entries: " . JournalEntry::count() . "\n";

// ========== SPECIFIC DATA CHECKS ==========
echo "\n\n=== SPECIFIC VALIDATIONS ===\n";

// Check product group LSA
echo "\nProduct Group LSA:\n";
ProductGroup::where('code', 'LSA')->with('products')->first();

// Check product author
echo "\nProduct with Author Info:\n";
Product::where('code', '04')->first();

// Check if invoice items are balanced
echo "\nChecking if Journal Entries are Balanced:\n";
JournalEntry::with('details')->get()->each(function($entry) {
    $totalDebit = $entry->details->sum('debit');
    $totalCredit = $entry->details->sum('credit');
    echo "Entry {$entry->entry_number}: Debit = {$totalDebit}, Credit = {$totalCredit} -> " . 
         ($totalDebit == $totalCredit ? "✓ BALANCED" : "✗ NOT BALANCED") . "\n";
});

?>
