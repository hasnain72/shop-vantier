<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\HomeCraftCard;
use App\Models\HomeCustomerReview;
use App\Models\HomeFaq;
use App\Models\HomeWorkshopItem;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class HomePageController extends Controller
{
    public function show(): JsonResponse
    {
        $keys = [
            'home_hero', 'home_craft_meta', 'home_new_arrivals_meta',
            'home_workshop_meta', 'home_featured_product',
            'home_featured_media_banner', 'home_apple_watch_promo',
            'home_trust', 'home_reviews_meta', 'home_faq_meta',
        ];

        $raw = StoreSetting::whereIn('key', $keys)->get()->keyBy('key');
        $get = fn (string $key) => isset($raw[$key])
            ? (json_decode($raw[$key]->value, true) ?? [])
            : [];

        return ApiResponse::success([
            'home' => [
                'hero_video'            => $this->heroVideo($get),
                'craft'                 => $this->craftSection($get),
                'new_arrivals'          => $this->newArrivalsSection($get),
                'workshop'              => $this->workshopSection($get),
                'featured_product'      => $this->featuredProduct($get),
                'featured_media_banner' => $this->featuredMediaBanner($get),
                'apple_watch_promo'     => $this->appleWatchPromo($get),
                'trust'                 => $this->trust($get),
                'customer_reviews'      => $this->customerReviewsSection($get),
                'faq'                   => $this->faqSection($get),
            ],
        ]);
    }

    // -----------------------------------------------------------------------
    // Section builders — each merges DB value over hardcoded defaults so
    // the response is always structurally complete even before the seeder runs.
    // -----------------------------------------------------------------------

    private function heroVideo(callable $get): array
    {
        return array_merge([
            'mp4_url' => '',
            'poster'  => null,
            'heading' => ['en' => '', 'ar' => ''],
        ], $get('home_hero'));
    }

    private function craftSection(callable $get): array
    {
        $meta = array_merge([
            'section_title'             => ['en' => 'The Vantier Craft',              'ar' => 'صنعة فانتييه'],
            'pagination_aria_label'     => ['en' => 'The Vantier Craft highlights',   'ar' => 'أبرز ما في صنعة فانتييه'],
            'pagination_dot_aria_label' => ['en' => 'Go to story',                    'ar' => 'انتقل إلى القصة'],
        ], $get('home_craft_meta'));

        $cards = $this->safeFetch(
            fn () => HomeCraftCard::orderBy('sort_order')->get()->map(fn ($c) => [
                'id'          => $c->id,
                'title'       => ['en' => $c->title_en,       'ar' => $c->title_ar],
                'description' => ['en' => $c->description_en, 'ar' => $c->description_ar],
                'image_url'   => $c->image_url,
            ])->values()->toArray()
        );

        return array_merge($meta, ['cards' => $cards]);
    }

    private function newArrivalsSection(callable $get): array
    {
        $meta = array_merge([
            'title'                    => ['en' => 'New Arrivals',           'ar' => 'وصل حديثاً'],
            'subtitle'                 => ['en' => '',                       'ar' => ''],
            'view_all_label'           => ['en' => 'View All',               'ar' => 'عرض الكل'],
            'view_all_path'            => '/shop',
            'select_options_label'     => ['en' => 'Select Options',         'ar' => 'اختر الخيارات'],
            'aria_wishlist'            => ['en' => 'Add to wishlist',         'ar' => 'أضف لقائمة الأمنيات'],
            'aria_compare'             => ['en' => 'Compare',                 'ar' => 'قارن'],
            'aria_quick_view'          => ['en' => 'Quick view',              'ar' => 'عرض سريع'],
            'pagination_aria_label'    => ['en' => 'New Arrivals products',   'ar' => 'منتجات وصل حديثاً'],
            'pagination_dot_aria_label'=> ['en' => 'Go to page',             'ar' => 'انتقل إلى الصفحة'],
            'count'                    => 5,
        ], $get('home_new_arrivals_meta'));

        $count    = (int) ($meta['count'] ?? 5);
        $products = $this->safeFetch(
            fn () => Product::with(['variants', 'productImages'])
                ->active()
                ->has('productImages')
                ->latest()
                ->take($count)
                ->get()
                ->map(fn ($p) => $this->mapProductToNewArrival($p))
                ->values()
                ->toArray()
        );

        return array_merge($meta, ['products' => $products]);
    }

    private function workshopSection(callable $get): array
    {
        $meta = array_merge([
            'section_title'              => ['en' => 'From the Workshop',              'ar' => 'من ورشة العمل'],
            'pagination_aria_label'      => ['en' => 'Workshop videos',                'ar' => 'فيديوهات ورشة العمل'],
            'pagination_dot_aria_label'  => ['en' => 'Go to video',                    'ar' => 'انتقل إلى الفيديو'],
            'card_open_modal_aria_label' => ['en' => 'Open workshop product details',  'ar' => 'فتح تفاصيل منتج الورشة'],
        ], $get('home_workshop_meta'));

        $items = $this->safeFetch(
            fn () => HomeWorkshopItem::active()->orderBy('sort_order')->get()->map(fn ($w) => [
                'id'                        => $w->id,
                'title'                     => ['en' => $w->title_en, 'ar' => $w->title_ar],
                'video_mp4'                 => $w->video_mp4,
                'video_poster'              => $w->video_poster,
                'thumb_url'                 => $w->thumb_url,
                'current_price'             => $w->current_price,
                'original_price'            => $w->original_price,
                'currency'                  => $w->currency,
                'product_path'              => $w->product_path,
                'modal_gallery_urls'        => $this->decodeJsonColumn($w->modal_gallery_urls),
                'modal_body_primary_html'   => $this->decodeJsonObject($w->modal_body_primary_html),
                'modal_body_secondary_html' => $this->decodeJsonObject($w->modal_body_secondary_html),
                'size_options'              => $this->decodeJsonColumn($w->size_options),
            ])->values()->toArray()
        );

        return array_merge($meta, ['items' => $items]);
    }

    private function featuredProduct(callable $get): array
    {
        return array_merge([
            'eyebrow'         => ['en' => '', 'ar' => ''],
            'title'           => ['en' => '', 'ar' => ''],
            'body'            => ['en' => '', 'ar' => ''],
            'image_url'       => '',
            'image_alt'       => ['en' => '', 'ar' => ''],
            'link_path'       => '/shop',
            'link_aria_label' => ['en' => '', 'ar' => ''],
        ], $get('home_featured_product'));
    }

    private function featuredMediaBanner(callable $get): array
    {
        return array_merge([
            'eyebrow'       => ['en' => '', 'ar' => ''],
            'title'         => ['en' => '', 'ar' => ''],
            'description'   => ['en' => '', 'ar' => ''],
            'cta_label'     => ['en' => '', 'ar' => ''],
            'cta_path'      => '/shop',
            'cta_aria_label'=> ['en' => '', 'ar' => ''],
            'video_mp4'     => '',
            'video_poster'  => null,
        ], $get('home_featured_media_banner'));
    }

    private function appleWatchPromo(callable $get): array
    {
        return array_merge([
            'eyebrow'           => ['en' => '', 'ar' => ''],
            'title'             => ['en' => '', 'ar' => ''],
            'subtitle'          => ['en' => '', 'ar' => ''],
            'cta_label'         => ['en' => '', 'ar' => ''],
            'cta_path'          => '/shop',
            'cta_aria_label'    => ['en' => '', 'ar' => ''],
            'banner_src_mobile' => '',
            'banner_src_desktop'=> '',
            'banner_src_default'=> '',
        ], $get('home_apple_watch_promo'));
    }

    private function trust(callable $get): array
    {
        return array_merge([
            'pagination_aria_label'     => ['en' => 'Trust highlights',  'ar' => 'ميزات الثقة'],
            'pagination_dot_aria_label' => ['en' => 'Go to highlight',   'ar' => 'انتقل إلى النقطة'],
            'items'      => [],
            'newsletter' => [
                'title'              => ['en' => '', 'ar' => ''],
                'email_placeholder'  => ['en' => '', 'ar' => ''],
                'submit_label'       => ['en' => '', 'ar' => ''],
                'disclaimer_intro'   => ['en' => '', 'ar' => ''],
                'privacy_link_label' => ['en' => '', 'ar' => ''],
                'disclaimer_outro'   => ['en' => '.', 'ar' => '.'],
                'privacy_path'       => '/contact',
            ],
        ], $get('home_trust'));
    }

    private function customerReviewsSection(callable $get): array
    {
        $meta = array_merge([
            'title'                => ['en' => 'What our customers say',         'ar' => 'ماذا يقول عملاؤنا'],
            'subtitle'             => ['en' => '',                               'ar' => ''],
            'verified_buyer_label' => ['en' => 'Verified Buyer',                 'ar' => 'مشتري موثق'],
            'rating_aria_label'    => ['en' => '5 out of 5 stars',               'ar' => '5 من 5 نجوم'],
        ], $get('home_reviews_meta'));

        $items = $this->safeFetch(
            fn () => HomeCustomerReview::active()->orderBy('sort_order')->get()->map(fn ($r) => [
                'id'                => $r->id,
                'author_name'       => $r->author_name,
                'review'            => ['en' => $r->review_en,       'ar' => $r->review_ar],
                'product_image_url' => $r->product_image_url,
                'product_name'      => ['en' => $r->product_name_en, 'ar' => $r->product_name_ar],
                'current_price'     => $r->current_price,
                'compare_at_price'  => $r->compare_at_price,
                'currency'          => ['en' => $r->currency_en,     'ar' => $r->currency_ar],
            ])->values()->toArray()
        );

        return array_merge($meta, ['items' => $items]);
    }

    private function faqSection(callable $get): array
    {
        $meta = array_merge([
            'title' => ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'],
        ], $get('home_faq_meta'));

        $items = $this->safeFetch(
            fn () => HomeFaq::active()->orderBy('sort_order')->get()->map(fn ($f) => [
                'id'       => $f->id,
                'question' => ['en' => $f->question_en, 'ar' => $f->question_ar],
                'answer'   => ['en' => $f->answer_en,   'ar' => $f->answer_ar],
            ])->values()->toArray()
        );

        return array_merge($meta, ['items' => $items]);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /** Run a DB query; return [] if the table doesn't exist yet (pre-migration). */
    private function safeFetch(callable $query): array
    {
        try {
            return $query();
        } catch (\Throwable) {
            return [];
        }
    }

    /**
     * Ensure a JSON array column is always returned as a PHP array.
     * Handles double-encoded values (seeder bug: json_encode on array-cast column).
     */
    private function decodeJsonColumn(mixed $value): array
    {
        if (is_array($value)) return $value;
        if (is_string($value)) return json_decode($value, true) ?? [];
        return [];
    }

    /**
     * Ensure a JSON object column is null or an associative array.
     * Handles double-encoded values from the seeder.
     */
    private function decodeJsonObject(mixed $value): ?array
    {
        if (is_null($value)) return null;
        if (is_array($value)) return $value;
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : null;
        }
        return null;
    }

    private function mapProductToNewArrival(Product $p): array
    {
        $images  = $p->productImages->sortBy('position');
        $default = $images->first();
        $hover   = $images->skip(1)->first() ?? $default;

        $variant   = $p->variants->where('is_active', true)->sortBy('price')->first();
        $price     = $variant ? number_format((float) $variant->price, 2) : '0.00';
        $compareAt = ($variant && $variant->compare_at_price)
            ? number_format((float) $variant->compare_at_price, 2)
            : null;

        return [
            'id'            => (string) $p->id,
            'title'         => ['en' => $p->title, 'ar' => $p->title],
            'current_price' => $price,
            'original_price'=> $compareAt,
            'currency'      => '$',
            'default_image' => $default ? Storage::disk('public')->url($default->src) : '',
            'hover_image'   => $hover   ? Storage::disk('public')->url($hover->src)   : '',
            'product_path'  => '/products/' . $p->slug,
        ];
    }
}
