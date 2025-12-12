# ForShare TaxService - Implementation Guide

## 🎯 **Overview**

ForShare TaxService adalah interface publik untuk module tax yang memungkinkan module lain menghitung pajak dengan mudah. Service ini mendukung semua use case kompleks termasuk compound tax, inclusive/exclusive taxes, dan historical rates.

## 📋 **Supported Use Cases**

### **Use Case 1: Restoran Berjenjang (Sequential Taxes)**

```php
// Service Charge 5% + PB1 10% berjenjang
$result = $taxService->calculateTax(100000, [
    'merchant_id' => 'resto-padang',
    'context' => 'dine_in'
]);

// Result: 100000 + 5000 + 10500 = 115500
echo $result->finalAmount; // 115500
echo $result->totalTax;    // 15500
```

### **Use Case 2: PPN Historical (Time-based)**

```php
// PPN otomatis berubah berdasarkan tanggal
$result = $taxService->calculateTax(100000, [
    'transaction_date' => '2025-01-15', // After rate change
    'region_id' => 'indonesia'
]);

// Result: 100000 + 12000 = 112000 (PPN 12%)
```

### **Use Case 3: Organization Multi-Branch**

```php
// Cabang Jakarta inherit tax dari Organization
$result = $taxService->calculateTax(100000, [
    'merchant_id' => 'cabang-jakarta-1',
    'organization_id' => 'pt-fastfood-indo',
    'region_id' => 'jakarta'
]);
```

### **Use Case 4: Inclusive Tax**

```php
// Harga 20000 sudah include PB1 10%
$result = $taxService->calculateTax(20000, [
    'merchant_id' => 'warung-kopi',
    'is_inclusive' => true
]);

// Display tetap 20000, tapi backend tahu:
// Base price: 18181.82, Tax: 1818.18
echo $result->finalAmount; // 20000 (unchanged)
echo $result->totalTax;    // 1818.18 (calculated tax portion)
```

### **Use Case 5: Fixed Fee (Handling)**

```php
// Biaya handling Rp 1000 per transaksi
$result = $taxService->calculateTax(0, [
    'include_handling_fee' => true
]);

echo $result->finalAmount; // 1000
```

## 🛠️ **API Methods**

### **calculateTax(float $amount, array $context = []): TaxResult**

Method utama untuk menghitung pajak dengan context lengkap.

**Parameters:**

-   `$amount`: Jumlah yang akan dihitung pajaknya
-   `$context`: Array associative dengan keys berikut:
    -   `merchant_id`: ID merchant (untuk pajak merchant-specific)
    -   `organization_id`: ID organization (untuk inheritance)
    -   `region_id`: ID region (untuk pajak regional)
    -   `product_id`: ID produk (untuk pajak produk-specific)
    -   `customer_id`: ID customer (untuk exemption)
    -   `transaction_date`: Tanggal transaksi (untuk historical rates)
    -   `is_inclusive`: Boolean (true untuk inclusive tax)
    -   `include_handling_fee`: Boolean (true untuk tambah biaya handling)
    -   `context`: String (dine_in, take_away, etc.)

**Return:** `TaxResult` object dengan properties:

-   `originalAmount`: Amount asli
-   `totalTax`: Total pajak
-   `finalAmount`: Amount akhir
-   `taxBreakdown`: Array breakdown per pajak
-   `isInclusive`: Boolean apakah inclusive
-   `appliedRules`: Array ID rules yang diterapkan
-   `calculationType`: Tipe kalkulasi (simple, compound, inclusive, mixed)

### **previewTax(float $amount, array $context = []): TaxPreview**

Method cepat untuk preview pajak tanpa kalkulasi detail.

**Return:** `TaxPreview` object dengan properties:

-   `estimatedTax`: Estimasi pajak
-   `taxRateDescription`: Deskripsi rate (contoh: "12% PPN")
-   `isTaxExempt`: Boolean apakah exempt
-   `exemptionReason`: Alasan exemption jika ada

## 🔧 **Context Examples**

### **Product Module:**

```php
$context = [
    'merchant_id' => 'merchant-123',
    'product_id' => 'product-456',
    'region_id' => 'jakarta'
];
```

### **Cart/Order Module:**

```php
$context = [
    'customer_id' => 'customer-789',
    'merchant_id' => 'merchant-123',
    'transaction_date' => '2025-01-15',
    'context' => 'dine_in',
    'region_id' => 'jakarta'
];
```

### **Payment Module:**

```php
$context = [
    'include_handling_fee' => true,
    'transaction_type' => 'payment'
];
```

## 📊 **Tax Calculation Logic**

### **1. Owner Hierarchy Resolution**

```
Priority: Merchant → Organization → System
```

-   Jika ada `merchant_id`: Gunakan pajak merchant + system
-   Jika ada `organization_id`: Gunakan pajak organization + system
-   Default: Hanya pajak system

### **2. Compound Tax Application**

```php
// Sequential application
$currentAmount = originalAmount;
foreach ($rules as $rule) {
    $taxAmount = calculateTax($currentAmount, $rule);
    if (!$rule->is_inclusive) {
        $currentAmount += $taxAmount;
    }
}
```

### **3. Inclusive vs Exclusive**

```php
// Exclusive (default): Tax ditambah ke harga
$taxAmount = $baseAmount * ($rate / 100);
$finalAmount = $baseAmount + $taxAmount;

// Inclusive: Tax sudah termasuk dalam harga
$taxAmount = ($finalAmount * $rate) / (100 + $rate);
$finalAmount = $finalAmount; // Unchanged
```

### **4. Historical Rates**

```php
// Automatic rate selection berdasarkan transaction_date
if ($transactionDate >= '2025-01-01') {
    $rate = 12; // New rate
} else {
    $rate = 11; // Old rate
}
```

## 🧪 **Testing Examples**

### **Test Compound Tax:**

```php
$result = $taxService->calculateTax(100000, [
    'merchant_id' => 'test-merchant-compound'
]);

// Expected: Service 5% + PB1 10% = 15500 total tax
assert($result->totalTax === 15500);
assert($result->finalAmount === 115500);
```

### **Test Inclusive Tax:**

```php
$result = $taxService->calculateTax(20000, [
    'merchant_id' => 'test-merchant-inclusive',
    'is_inclusive' => true
]);

// Expected: Display 20000, tax portion 1818.18
assert($result->finalAmount === 20000);
assert($result->isInclusive === true);
```

### **Test Fixed Fee:**

```php
$result = $taxService->calculateTax(50000, [
    'include_handling_fee' => true
]);

// Expected: 50000 + 1000 handling = 51000
assert($result->finalAmount === 51000);
```

## 🔄 **Backward Compatibility**

Service ini tetap support method lama untuk compatibility:

```php
// Legacy method (masih work)
$tax = $taxService->calculateTaxLegacy(100000, 'jakarta', 'food', 'merchant-123');

// New method (recommended)
$result = $taxService->calculateTax(100000, [
    'region_id' => 'jakarta',
    'category_id' => 'food',
    'merchant_id' => 'merchant-123'
]);
```

## 🚀 **Integration Steps**

1. **Inject service** di module yang perlu:

```php
use App\Shared\Tax\ITaxService;

class ProductService
{
    public function __construct(private ITaxService $taxService) {}

    public function calculateProductPrice(float $basePrice, array $context): float
    {
        $result = $this->taxService->calculateTax($basePrice, $context);
        return $result->finalAmount;
    }
}
```

2. **Call dengan context sesuai kebutuhan**:

```php
$finalPrice = $productService->calculateProductPrice(100000, [
    'merchant_id' => $merchantId,
    'product_id' => $productId,
    'region_id' => $regionId
]);
```

3. **Handle result object**:

```php
$result = $taxService->calculateTax($amount, $context);

// Display price
$displayPrice = $result->finalAmount;

// Show tax breakdown (optional)
foreach ($result->taxBreakdown as $tax) {
    echo "{$tax['name']}: Rp " . number_format($tax['amount']);
}
```

## ✅ **Features Summary**

-   ✅ **Compound Tax**: Sequential application (Service + PB1)
-   ✅ **Inclusive/Exclusive**: Support keduanya
-   ✅ **Fixed Amounts**: Handling fees, dll
-   ✅ **Historical Rates**: Time-based rate changes
-   ✅ **Owner Hierarchy**: System → Organization → Merchant
-   ✅ **Context Flexibility**: Extensible context array
-   ✅ **Error Handling**: Graceful fallback
-   ✅ **Performance**: Preview method untuk UI cepat
-   ✅ **Backward Compatible**: Support legacy methods
-   ✅ **Type Safe**: Strong typing dengan return objects

**ForShare TaxService siap digunakan oleh semua module!** 🎉
