<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $items = $this->items->map(function ($item) {
            $variant  = $item->variant;
            $product  = $variant?->product;
            $imgPath  = $product?->featured_image;

            return [
                'id'               => $item->id,
                'variant_id'       => $item->variant_id,
                'product_id'       => $variant?->product_id,
                'title'            => $product?->title ?? '—',
                'variant_title'    => $variant?->title,
                'sku'              => $variant?->sku,
                'image'            => $imgPath ? Storage::disk('public')->url($imgPath) : null,
                'price'            => (float) $item->price,
                'original_price'   => (float) ($variant?->price ?? $item->price),
                'quantity'         => $item->quantity,
                'total_price'      => round((float) $item->price * $item->quantity, 2),
                'properties'       => $item->properties ?? [],
            ];
        });

        $itemCount  = $items->sum('quantity');
        $totalPrice = $items->sum('total_price');

        return [
            'token'             => $this->token,
            'note'              => $this->note,
            'attributes'        => $this->attributes ?? [],
            'currency'          => $this->currency ?? 'USD',
            'item_count'        => $itemCount,
            'total_price'       => $totalPrice,
            'total_discount'    => 0.0,
            'requires_shipping' => $this->requires_shipping,
            'items'             => $items->values(),
        ];
    }
}
