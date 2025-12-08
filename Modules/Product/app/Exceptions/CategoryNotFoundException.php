<?php

namespace Modules\Product\Exceptions;

use App\Exceptions\BaseException;

/**
 * Exception thrown when a category is not found
 */
class CategoryNotFoundException extends BaseException
{
    /**
     * Create a new CategoryNotFoundException instance
     *
     * @param string $categoryId The category ID that was not found
     * @param array $context Additional context data
     */
    public function __construct(string $categoryId, array $context = [])
    {
        $context['category_id'] = $categoryId;
        parent::__construct('exception.category.not_found', $context, 'product', 404);
    }
}
