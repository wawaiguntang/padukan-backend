<?php

namespace Modules\Product\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Shared\Merchant\Services\IMerchantService as ISharedMerchantService;
use Symfony\Component\HttpFoundation\Response;

/**
 * Merchant Access Middleware for Product Module
 *
 * This middleware validates merchant context and ownership for product operations
 */
class MerchantAccessMiddleware
{
    private ISharedMerchantService $merchantService;

    public function __construct(ISharedMerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * Handle an incoming request
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract merchant ID from request headers or route parameters
        $merchantId = $this->extractMerchantId($request);

        if (!$merchantId) {
            return response()->json([
                'status' => false,
                'message' => __('product::middleware.merchant_id_required'),
            ], 400);
        }

        // Validate merchant exists
        $merchant = $this->merchantService->getMerchantById($merchantId);

        if (!$merchant) {
            return response()->json([
                'status' => false,
                'message' => __('product::middleware.merchant_not_found'),
            ], 404);
        }

        // Validate merchant is active
        if ($merchant['verification_status'] !== 'approved') {
            return response()->json([
                'status' => false,
                'message' => __('product::middleware.merchant_not_approved'),
            ], 403);
        }

        // Validate user has access to the merchant
        $userId = $request->authenticated_user_id;

        if (!$this->merchantService->checkOwnership($userId, $merchantId)) {
            return response()->json([
                'status' => false,
                'message' => __('product::middleware.merchant_access_denied'),
            ], 403);
        }

        // Set merchant context for the request
        $request->merge([
            'merchant' => $merchant,
        ]);

        return $next($request);
    }

    /**
     * Extract merchant ID from request
     *
     * @param Request $request
     * @return string|null
     */
    private function extractMerchantId(Request $request): ?string
    {
        // Try to get from header first
        $merchantId = $request->header('X-Merchant-ID');

        // If not in header, try route parameter
        if (!$merchantId) {
            $merchantId = $request->route('merchantId');
        }

        // If not in route, try other common parameter names
        if (!$merchantId) {
            $merchantId = $request->route('merchant_id');
        }

        return $merchantId;
    }
}
