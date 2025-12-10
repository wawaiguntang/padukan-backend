<?php

namespace Modules\Product\Events;

use Illuminate\Queue\SerializesModels;
use Modules\Product\Models\Product;

class ProductUpdated
{
    use SerializesModels;

    public function __construct(
        public Product $product
    ) {}
}
