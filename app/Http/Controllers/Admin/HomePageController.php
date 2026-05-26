<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeCraftCard;
use App\Models\HomeCustomerReview;
use App\Models\HomeFaq;
use App\Models\HomeWorkshopItem;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    // ─── Settings ────────────────────────────────────────────────────────────

    public function settings()
    {
        $keys = [
            'home_hero', 'home_new_arrivals_meta', 'home_workshop_meta',
            'home_featured_product', 'home_featured_media_banner',
            'home_apple_watch_promo', 'home_trust', 'home_reviews_meta', 'home_faq_meta',
        ];
        $raw  = StoreSetting::whereIn('key', $keys)->get()->keyBy('key');
        $s    = fn (string $k) => isset($raw[$k]) ? json_decode($raw[$k]->value, true) : [];

        return view('admin.home.settings', [
            'hero'                => $s('home_hero'),
            'newArrivalsMeta'     => $s('home_new_arrivals_meta'),
            'workshopMeta'        => $s('home_workshop_meta'),
            'featuredProduct'     => $s('home_featured_product'),
            'featuredMediaBanner' => $s('home_featured_media_banner'),
            'appleWatchPromo'     => $s('home_apple_watch_promo'),
            'trust'               => $s('home_trust'),
            'reviewsMeta'         => $s('home_reviews_meta'),
            'faqMeta'             => $s('home_faq_meta'),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $r = $request;

        $this->saveSetting('home_hero', [
            'mp4_url' => $r->input('hero_mp4_url', ''),
            'poster'  => $r->input('hero_poster') ?: null,
            'heading' => ['en' => $r->input('hero_heading_en', ''), 'ar' => $r->input('hero_heading_ar', '')],
        ]);

        $this->saveSetting('home_new_arrivals_meta', [
            'count'                    => (int) $r->input('na_count', 5),
            'title'                    => ['en' => $r->input('na_title_en', ''),    'ar' => $r->input('na_title_ar', '')],
            'subtitle'                 => ['en' => $r->input('na_subtitle_en', ''), 'ar' => $r->input('na_subtitle_ar', '')],
            'view_all_label'           => ['en' => $r->input('na_view_all_en', ''), 'ar' => $r->input('na_view_all_ar', '')],
            'view_all_path'            => $r->input('na_view_all_path', '/shop'),
            'select_options_label'     => ['en' => 'Select Options', 'ar' => 'اختر الخيارات'],
            'aria_wishlist'            => ['en' => 'Add to wishlist', 'ar' => 'أضف لقائمة الأمنيات'],
            'aria_compare'             => ['en' => 'Compare',        'ar' => 'قارن'],
            'aria_quick_view'          => ['en' => 'Quick view',     'ar' => 'عرض سريع'],
            'pagination_aria_label'    => ['en' => 'New Arrivals products', 'ar' => 'منتجات وصل حديثاً'],
            'pagination_dot_aria_label'=> ['en' => 'Go to page',    'ar' => 'انتقل إلى الصفحة'],
        ]);

        $this->saveSetting('home_featured_product', [
            'eyebrow'        => ['en' => $r->input('fp_eyebrow_en', ''),    'ar' => $r->input('fp_eyebrow_ar', '')],
            'title'          => ['en' => $r->input('fp_title_en', ''),      'ar' => $r->input('fp_title_ar', '')],
            'body'           => ['en' => $r->input('fp_body_en', ''),       'ar' => $r->input('fp_body_ar', '')],
            'image_url'      => $r->input('fp_image_url', ''),
            'image_alt'      => ['en' => $r->input('fp_image_alt_en', ''), 'ar' => $r->input('fp_image_alt_ar', '')],
            'link_path'      => $r->input('fp_link_path', '/shop'),
            'link_aria_label'=> ['en' => $r->input('fp_link_aria_en', ''), 'ar' => $r->input('fp_link_aria_ar', '')],
        ]);

        $this->saveSetting('home_featured_media_banner', [
            'eyebrow'       => ['en' => $r->input('fmb_eyebrow_en', ''),    'ar' => $r->input('fmb_eyebrow_ar', '')],
            'title'         => ['en' => $r->input('fmb_title_en', ''),      'ar' => $r->input('fmb_title_ar', '')],
            'description'   => ['en' => $r->input('fmb_desc_en', ''),       'ar' => $r->input('fmb_desc_ar', '')],
            'cta_label'     => ['en' => $r->input('fmb_cta_label_en', ''),  'ar' => $r->input('fmb_cta_label_ar', '')],
            'cta_path'      => $r->input('fmb_cta_path', '/shop'),
            'cta_aria_label'=> ['en' => $r->input('fmb_cta_aria_en', ''),  'ar' => $r->input('fmb_cta_aria_ar', '')],
            'video_mp4'     => $r->input('fmb_video_mp4', ''),
            'video_poster'  => $r->input('fmb_video_poster') ?: null,
        ]);

        $this->saveSetting('home_apple_watch_promo', [
            'eyebrow'           => ['en' => $r->input('aw_eyebrow_en', ''),    'ar' => $r->input('aw_eyebrow_ar', '')],
            'title'             => ['en' => $r->input('aw_title_en', ''),      'ar' => $r->input('aw_title_ar', '')],
            'subtitle'          => ['en' => $r->input('aw_subtitle_en', ''),   'ar' => $r->input('aw_subtitle_ar', '')],
            'cta_label'         => ['en' => $r->input('aw_cta_label_en', ''),  'ar' => $r->input('aw_cta_label_ar', '')],
            'cta_path'          => $r->input('aw_cta_path', '/shop'),
            'cta_aria_label'    => ['en' => $r->input('aw_cta_aria_en', ''),  'ar' => $r->input('aw_cta_aria_ar', '')],
            'banner_src_mobile' => $r->input('aw_banner_mobile', ''),
            'banner_src_desktop'=> $r->input('aw_banner_desktop', ''),
            'banner_src_default'=> $r->input('aw_banner_desktop', ''),
        ]);

        $existing = StoreSetting::where('key', 'home_trust')->first();
        $trust     = $existing ? json_decode($existing->value, true) : [];
        $this->saveSetting('home_trust', array_merge($trust, [
            'newsletter' => [
                'title'              => ['en' => $r->input('trust_nl_title_en', ''),    'ar' => $r->input('trust_nl_title_ar', '')],
                'email_placeholder'  => ['en' => $r->input('trust_nl_email_en', ''),   'ar' => $r->input('trust_nl_email_ar', '')],
                'submit_label'       => ['en' => $r->input('trust_nl_submit_en', ''),  'ar' => $r->input('trust_nl_submit_ar', '')],
                'disclaimer_intro'   => ['en' => $r->input('trust_nl_disclaimer_en',''),'ar'=> $r->input('trust_nl_disclaimer_ar','')],
                'privacy_link_label' => ['en' => $r->input('trust_nl_privacy_en', ''), 'ar' => $r->input('trust_nl_privacy_ar', '')],
                'disclaimer_outro'   => ['en' => '.', 'ar' => '.'],
                'privacy_path'       => $r->input('trust_nl_privacy_path', '/contact'),
            ],
        ]));

        $this->saveSetting('home_reviews_meta', [
            'title'                => ['en' => $r->input('rev_title_en', ''),   'ar' => $r->input('rev_title_ar', '')],
            'subtitle'             => ['en' => $r->input('rev_sub_en', ''),     'ar' => $r->input('rev_sub_ar', '')],
            'verified_buyer_label' => ['en' => 'Verified Buyer',                'ar' => 'مشتري موثق'],
            'rating_aria_label'    => ['en' => '5 out of 5 stars',             'ar' => '5 من 5 نجوم'],
        ]);

        $this->saveSetting('home_faq_meta', [
            'title' => ['en' => $r->input('faq_title_en', 'FAQ'), 'ar' => $r->input('faq_title_ar', 'الأسئلة الشائعة')],
        ]);

        return redirect()->route('admin.home.settings')->with('success', 'Home page settings saved.');
    }

    // ─── Craft Cards ─────────────────────────────────────────────────────────

    public function craftCards()
    {
        return view('admin.home.craft-cards.index', [
            'cards' => HomeCraftCard::orderBy('sort_order')->get(),
        ]);
    }

    public function createCraftCard()
    {
        return view('admin.home.craft-cards.form', ['card' => null]);
    }

    public function storeCraftCard(Request $request)
    {
        $data = $request->validate([
            'title_en'       => 'required|string|max:200',
            'title_ar'       => 'required|string|max:200',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'image_url'      => 'required|url',
            'sort_order'     => 'integer|min:0',
        ]);
        HomeCraftCard::create($data);
        return redirect()->route('admin.home.craft-cards')->with('success', 'Card added.');
    }

    public function editCraftCard(HomeCraftCard $card)
    {
        return view('admin.home.craft-cards.form', compact('card'));
    }

    public function updateCraftCard(Request $request, HomeCraftCard $card)
    {
        $data = $request->validate([
            'title_en'       => 'required|string|max:200',
            'title_ar'       => 'required|string|max:200',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'image_url'      => 'required|url',
            'sort_order'     => 'integer|min:0',
        ]);
        $card->update($data);
        return redirect()->route('admin.home.craft-cards')->with('success', 'Card updated.');
    }

    public function destroyCraftCard(HomeCraftCard $card)
    {
        $card->delete();
        return back()->with('success', 'Card deleted.');
    }

    // ─── Workshop Items ───────────────────────────────────────────────────────

    public function workshopItems()
    {
        return view('admin.home.workshop.index', [
            'items' => HomeWorkshopItem::orderBy('sort_order')->get(),
        ]);
    }

    public function createWorkshopItem()
    {
        return view('admin.home.workshop.form', ['item' => null]);
    }

    public function storeWorkshopItem(Request $request)
    {
        $data = $this->validateWorkshopItem($request);
        $data = $this->prepareWorkshopData($request, $data);
        HomeWorkshopItem::create($data);
        return redirect()->route('admin.home.workshop')->with('success', 'Workshop item added.');
    }

    public function editWorkshopItem(HomeWorkshopItem $item)
    {
        return view('admin.home.workshop.form', compact('item'));
    }

    public function updateWorkshopItem(Request $request, HomeWorkshopItem $item)
    {
        $data = $this->validateWorkshopItem($request);
        $data = $this->prepareWorkshopData($request, $data);
        $item->update($data);
        return redirect()->route('admin.home.workshop')->with('success', 'Workshop item updated.');
    }

    public function destroyWorkshopItem(HomeWorkshopItem $item)
    {
        $item->delete();
        return back()->with('success', 'Workshop item deleted.');
    }

    public function toggleWorkshopItem(HomeWorkshopItem $item)
    {
        $item->update(['is_active' => ! $item->is_active]);
        return back()->with('success', 'Status updated.');
    }

    // ─── Customer Reviews ─────────────────────────────────────────────────────

    public function reviews()
    {
        return view('admin.home.reviews.index', [
            'reviews' => HomeCustomerReview::orderBy('sort_order')->get(),
        ]);
    }

    public function createReview()
    {
        return view('admin.home.reviews.form', ['review' => null]);
    }

    public function storeReview(Request $request)
    {
        $data = $request->validate([
            'author_name'       => 'required|string|max:150',
            'review_en'         => 'required|string',
            'review_ar'         => 'required|string',
            'product_image_url' => 'required|url',
            'product_name_en'   => 'required|string|max:200',
            'product_name_ar'   => 'required|string|max:200',
            'current_price'     => 'required|string|max:30',
            'compare_at_price'  => 'nullable|string|max:30',
            'currency_en'       => 'required|string|max:20',
            'currency_ar'       => 'required|string|max:20',
            'sort_order'        => 'integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        HomeCustomerReview::create($data);
        return redirect()->route('admin.home.reviews')->with('success', 'Review added.');
    }

    public function editReview(HomeCustomerReview $review)
    {
        return view('admin.home.reviews.form', compact('review'));
    }

    public function updateReview(Request $request, HomeCustomerReview $review)
    {
        $data = $request->validate([
            'author_name'       => 'required|string|max:150',
            'review_en'         => 'required|string',
            'review_ar'         => 'required|string',
            'product_image_url' => 'required|url',
            'product_name_en'   => 'required|string|max:200',
            'product_name_ar'   => 'required|string|max:200',
            'current_price'     => 'required|string|max:30',
            'compare_at_price'  => 'nullable|string|max:30',
            'currency_en'       => 'required|string|max:20',
            'currency_ar'       => 'required|string|max:20',
            'sort_order'        => 'integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $review->update($data);
        return redirect()->route('admin.home.reviews')->with('success', 'Review updated.');
    }

    public function destroyReview(HomeCustomerReview $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }

    public function toggleReview(HomeCustomerReview $review)
    {
        $review->update(['is_active' => ! $review->is_active]);
        return back()->with('success', 'Status updated.');
    }

    // ─── FAQs ─────────────────────────────────────────────────────────────────

    public function faqs()
    {
        return view('admin.home.faqs.index', [
            'faqs' => HomeFaq::orderBy('sort_order')->get(),
        ]);
    }

    public function createFaq()
    {
        return view('admin.home.faqs.form', ['faq' => null]);
    }

    public function storeFaq(Request $request)
    {
        $data = $request->validate([
            'question_en' => 'required|string',
            'question_ar' => 'required|string',
            'answer_en'   => 'required|string',
            'answer_ar'   => 'required|string',
            'sort_order'  => 'integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);
        HomeFaq::create($data);
        return redirect()->route('admin.home.faqs')->with('success', 'FAQ added.');
    }

    public function editFaq(HomeFaq $faq)
    {
        return view('admin.home.faqs.form', compact('faq'));
    }

    public function updateFaq(Request $request, HomeFaq $faq)
    {
        $data = $request->validate([
            'question_en' => 'required|string',
            'question_ar' => 'required|string',
            'answer_en'   => 'required|string',
            'answer_ar'   => 'required|string',
            'sort_order'  => 'integer|min:0',
        ]);
        $data['is_active'] = $request->boolean('is_active');
        $faq->update($data);
        return redirect()->route('admin.home.faqs')->with('success', 'FAQ updated.');
    }

    public function destroyFaq(HomeFaq $faq)
    {
        $faq->delete();
        return back()->with('success', 'FAQ deleted.');
    }

    public function toggleFaq(HomeFaq $faq)
    {
        $faq->update(['is_active' => ! $faq->is_active]);
        return back()->with('success', 'Status updated.');
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function saveSetting(string $key, array $value): void
    {
        StoreSetting::updateOrCreate(
            ['key' => $key],
            ['value' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
        );
    }

    private function validateWorkshopItem(Request $request): array
    {
        return $request->validate([
            'title_en'      => 'required|string|max:200',
            'title_ar'      => 'required|string|max:200',
            'video_mp4'     => 'required|url',
            'video_poster'  => 'nullable|url',
            'thumb_url'     => 'required|url',
            'current_price' => 'required|string|max:30',
            'original_price'=> 'nullable|string|max:30',
            'currency'      => 'required|string|max:10',
            'product_path'  => 'nullable|string|max:255',
            'sort_order'    => 'integer|min:0',
        ]);
    }

    private function prepareWorkshopData(Request $request, array $data): array
    {
        $galleryRaw = $request->input('modal_gallery_urls', '');
        $data['modal_gallery_urls'] = array_values(array_filter(
            array_map('trim', explode("\n", $galleryRaw)),
        ));

        $data['modal_body_primary_html']   = $request->filled('modal_primary_en')
            ? ['en' => $request->input('modal_primary_en'), 'ar' => $request->input('modal_primary_ar', '')]
            : null;

        $data['modal_body_secondary_html'] = $request->filled('modal_secondary_en')
            ? ['en' => $request->input('modal_secondary_en'), 'ar' => $request->input('modal_secondary_ar', '')]
            : null;

        $sizeIds    = $request->input('size_ids', []);
        $sizeLabels = $request->input('size_labels_en', []);
        $sizeAr     = $request->input('size_labels_ar', []);
        $sizeVar    = $request->input('size_variants', []);

        $sizes = [];
        foreach ($sizeIds as $i => $id) {
            if (empty($id)) continue;
            $sizes[] = array_filter([
                'id'      => $id,
                'label'   => ['en' => $sizeLabels[$i] ?? '', 'ar' => $sizeAr[$i] ?? ''],
                'variant' => $sizeVar[$i] === 'apple' ? 'apple' : null,
            ], fn ($v) => $v !== null);
        }
        $data['size_options'] = $sizes;
        $data['is_active']    = $request->boolean('is_active', true);

        return $data;
    }
}
