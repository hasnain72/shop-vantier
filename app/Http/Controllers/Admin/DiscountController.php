<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDiscountRequest;
use App\Models\Collection;
use App\Models\DiscountCode;
use App\Models\PriceRule;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all');

        $query = PriceRule::withCount('discountCodes')
            ->with('discountCodes')
            ->latest();

        $query->when($filter === 'active', fn ($q) =>
            $q->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
              ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
        )->when($filter === 'expired', fn ($q) =>
            $q->whereNotNull('ends_at')->where('ends_at', '<', now())
        )->when($filter === 'scheduled', fn ($q) =>
            $q->whereNotNull('starts_at')->where('starts_at', '>', now())
        );

        $priceRules = $query->paginate(20)->withQueryString();

        return view('admin.discounts.index', compact('priceRules', 'filter'));
    }

    public function create()
    {
        $collections = Collection::published()->orderBy('title')->get();
        $products    = Product::where('status', 'active')->orderBy('title')->get();
        return view('admin.discounts.create', compact('collections', 'products'));
    }

    public function store(StoreDiscountRequest $request)
    {
        DB::transaction(function () use ($request) {
            $rule = PriceRule::create([
                'title'                          => $request->title,
                'value_type'                     => $request->value_type,
                'value'                          => $request->value_type !== 'free_shipping' ? -abs($request->value) : 0,
                'target_type'                    => $request->target_type ?? 'line_item',
                'target_selection'               => $request->target_selection ?? 'all',
                'allocation_method'              => 'across',
                'customer_selection'             => $request->customer_selection ?? 'all',
                'once_per_customer'              => $request->boolean('once_per_customer'),
                'usage_limit'                    => $request->usage_limit,
                'usage_count'                    => 0,
                'starts_at'                      => $request->starts_at,
                'ends_at'                        => $request->ends_at,
                'prerequisite_subtotal_range'    => $request->min_purchase_amount
                    ? ['greater_than_or_equal_to' => $request->min_purchase_amount] : null,
                'prerequisite_quantity_range'    => $request->min_quantity
                    ? ['greater_than_or_equal_to' => $request->min_quantity] : null,
                'entitled_collection_ids'        => $request->entitled_collection_ids ?? [],
                'entitled_product_ids'           => $request->entitled_product_ids ?? [],
            ]);

            foreach ($request->codes ?? [] as $code) {
                if (!empty($code)) {
                    DiscountCode::create([
                        'price_rule_id' => $rule->id,
                        'code'          => strtoupper(trim($code)),
                        'usage_count'   => 0,
                    ]);
                }
            }
        });

        return redirect()->route('admin.discounts.index')->with('success', 'Discount created.');
    }

    public function edit(PriceRule $discount)
    {
        $discount->load('discountCodes');
        $collections = Collection::published()->orderBy('title')->get();
        $products    = Product::where('status', 'active')->orderBy('title')->get();
        return view('admin.discounts.edit', compact('discount', 'collections', 'products'));
    }

    public function update(StoreDiscountRequest $request, PriceRule $discount)
    {
        DB::transaction(function () use ($request, $discount) {
            $discount->update([
                'title'                       => $request->title,
                'value_type'                  => $request->value_type,
                'value'                       => $request->value_type !== 'free_shipping' ? -abs($request->value) : 0,
                'target_type'                 => $request->target_type ?? 'line_item',
                'target_selection'            => $request->target_selection ?? 'all',
                'customer_selection'          => $request->customer_selection ?? 'all',
                'once_per_customer'           => $request->boolean('once_per_customer'),
                'usage_limit'                 => $request->usage_limit,
                'starts_at'                   => $request->starts_at,
                'ends_at'                     => $request->ends_at,
                'prerequisite_subtotal_range' => $request->min_purchase_amount
                    ? ['greater_than_or_equal_to' => $request->min_purchase_amount] : null,
                'prerequisite_quantity_range' => $request->min_quantity
                    ? ['greater_than_or_equal_to' => $request->min_quantity] : null,
                'entitled_collection_ids'     => $request->entitled_collection_ids ?? [],
                'entitled_product_ids'        => $request->entitled_product_ids ?? [],
            ]);

            // Sync codes: delete removed, add new
            $existing = $discount->discountCodes->pluck('code')->toArray();
            $incoming = array_map('strtoupper', array_filter($request->codes ?? []));

            // Add new codes
            foreach (array_diff($incoming, $existing) as $code) {
                DiscountCode::create([
                    'price_rule_id' => $discount->id,
                    'code'          => $code,
                    'usage_count'   => 0,
                ]);
            }
        });

        return redirect()->route('admin.discounts.index')->with('success', 'Discount updated.');
    }

    public function destroy(PriceRule $discount)
    {
        $discount->discountCodes()->delete();
        $discount->delete();
        return back()->with('success', 'Discount deleted.');
    }

    public function generateCode()
    {
        return response()->json(['code' => strtoupper(Str::random(8))]);
    }
}
