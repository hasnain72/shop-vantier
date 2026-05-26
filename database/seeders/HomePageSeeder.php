<?php

namespace Database\Seeders;

use App\Models\HomeCraftCard;
use App\Models\HomeCustomerReview;
use App\Models\HomeFaq;
use App\Models\HomeWorkshopItem;
use App\Models\StoreSetting;
use Illuminate\Database\Seeder;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedCraftCards();
        $this->seedWorkshopItems();
        $this->seedCustomerReviews();
        $this->seedFaqs();
    }

    // -----------------------------------------------------------------------
    // Store settings (JSON blobs for CMS-controlled sections)
    // -----------------------------------------------------------------------

    private function seedSettings(): void
    {
        $settings = [
            'home_hero' => [
                'mp4_url' => 'https://cdn.shopify.com/videos/c/o/v/7d1f0eae88b746e9a8594d855f300f25.mp4',
                'poster'  => null,
                'heading' => ['en' => 'THE FINEST ITALIAN HIDE', 'ar' => 'أجود الجلود الإيطالية'],
            ],

            'home_craft_meta' => [
                'section_title'            => ['en' => 'The Vantier Craft',        'ar' => 'صنعة فانتييه'],
                'pagination_aria_label'    => ['en' => 'The Vantier Craft highlights', 'ar' => 'أبرز ما في صنعة فانتييه'],
                'pagination_dot_aria_label'=> ['en' => 'Go to story',              'ar' => 'انتقل إلى القصة'],
            ],

            'home_new_arrivals_meta' => [
                'title'                    => ['en' => 'New Arrivals',          'ar' => 'وصل حديثاً'],
                'subtitle'                 => ['en' => 'Unmatched design—superior performance and customer satisfaction in one.', 'ar' => 'تصميم لا مثيل له—أداء راقٍ ورضا العملاء في تجربة واحدة.'],
                'view_all_label'           => ['en' => 'View All',              'ar' => 'عرض الكل'],
                'view_all_path'            => '/shop',
                'select_options_label'     => ['en' => 'Select Options',        'ar' => 'اختر الخيارات'],
                'aria_wishlist'            => ['en' => 'Add to wishlist',        'ar' => 'أضف لقائمة الأمنيات'],
                'aria_compare'             => ['en' => 'Compare',                'ar' => 'قارن'],
                'aria_quick_view'          => ['en' => 'Quick view',             'ar' => 'عرض سريع'],
                'pagination_aria_label'    => ['en' => 'New Arrivals products',  'ar' => 'منتجات وصل حديثاً'],
                'pagination_dot_aria_label'=> ['en' => 'Go to page',            'ar' => 'انتقل إلى الصفحة'],
                'count'                    => 5,
            ],

            'home_workshop_meta' => [
                'section_title'              => ['en' => 'From the Workshop',             'ar' => 'من ورشة العمل'],
                'pagination_aria_label'      => ['en' => 'Workshop videos',               'ar' => 'فيديوهات ورشة العمل'],
                'pagination_dot_aria_label'  => ['en' => 'Go to video',                   'ar' => 'انتقل إلى الفيديو'],
                'card_open_modal_aria_label' => ['en' => 'Open workshop product details', 'ar' => 'فتح تفاصيل منتج الورشة'],
            ],

            'home_featured_product' => [
                'eyebrow'        => ['en' => 'Featured',                             'ar' => 'مختار'],
                'title'          => ['en' => 'Original Ostrich Leather',             'ar' => 'جلد النعام الأصلي'],
                'body'           => ['en' => 'Made with care and unconditionally loved by our customers, this signature bestseller exceeds all expectations.', 'ar' => 'صُنع بعناية ويحظى بحب عملائنا دون قيد أو شرط، وهذا الأكثر مبيعاً المميز يفوق كل التوقعات.'],
                'image_url'      => 'https://thevantier.com/cdn/shop/files/ventier-brown-ostrich-watch-strap-angled-2.png',
                'image_alt'      => ['en' => 'Tan ostrich leather watch straps',    'ar' => 'أساور ساعة من جلد النعام بلون العسلي'],
                'link_path'      => '/shop',
                'link_aria_label'=> ['en' => 'Shop Original Ostrich Leather',       'ar' => 'تسوق جلد النعام الأصلي'],
            ],

            'home_featured_media_banner' => [
                'eyebrow'       => ['en' => 'Featured',                          'ar' => 'مختار'],
                'title'         => ['en' => 'Premium Alligator Series',          'ar' => 'سلسلة تمساح فاخرة'],
                'description'   => ['en' => 'Hand-finished alligator leather. Precision stitching. Signature steel buckle.', 'ar' => 'جلد تمساح منتهي يدوياً. خياطة دقيقة. إبزيم فولاذي مميز.'],
                'cta_label'     => ['en' => 'Explore Now',                       'ar' => 'استكشف الآن'],
                'cta_path'      => '/shop',
                'cta_aria_label'=> ['en' => 'Explore Premium Alligator Series',  'ar' => 'استكشف سلسلة التمساح الفاخرة'],
                'video_mp4'     => 'https://thevantier.com/cdn/shop/videos/c/vp/a751acc3b8b24e8e996be4ae1af34254/a751acc3b8b24e8e996be4ae1af34254.HD-720p-3.0Mbps-63180309.mp4?v=0',
                'video_poster'  => 'https://thevantier.com/cdn/shop/files/preview_images/a751acc3b8b24e8e996be4ae1af34254.thumbnail.0000000000.jpg?v=1763500228&width=1920',
            ],

            'home_apple_watch_promo' => [
                'eyebrow'           => ['en' => 'For Apple Watch',                'ar' => 'لساعة آبل'],
                'title'             => ['en' => 'Elevate Your Apple Watch',       'ar' => 'ارتقِ بساعة آبل'],
                'subtitle'          => ['en' => 'All of our straps are compatible with the Apple Watches', 'ar' => 'جميع أساورنا متوافقة مع ساعات آبل'],
                'cta_label'         => ['en' => 'Shop now',                       'ar' => 'تسوق الآن'],
                'cta_path'          => '/shop',
                'cta_aria_label'    => ['en' => 'Shop Apple Watch straps',        'ar' => 'تسوق أساور ساعة آبل'],
                'banner_src_mobile' => 'https://thevantier.com/cdn/shop/files/Black_and_White_Simple_Name_LinkedIn_Article_Cover_Image_5.png?v=1772644730&width=1000',
                'banner_src_desktop'=> 'https://thevantier.com/cdn/shop/files/Black_and_White_Simple_Name_LinkedIn_Article_Cover_Image_5.png?v=1772644730&width=2000',
                'banner_src_default'=> 'https://thevantier.com/cdn/shop/files/Black_and_White_Simple_Name_LinkedIn_Article_Cover_Image_5.png?v=1772644730&width=2000',
            ],

            'home_trust' => [
                'pagination_aria_label'    => ['en' => 'Trust and delivery highlights', 'ar' => 'ميزات الثقة والتوصيل'],
                'pagination_dot_aria_label'=> ['en' => 'Go to highlight',               'ar' => 'انتقل إلى النقطة'],
                'items' => [
                    [
                        'id'          => 'trust-1',
                        'icon_kind'   => 'payment',
                        'title'       => ['en' => 'Trust & Security',      'ar' => 'الثقة والأمان'],
                        'description' => ['en' => 'Enjoy safe and secure shopping with 100% secure payment from a trusted, verified store.', 'ar' => 'تسوّق بأمان مع دفع آمن بالكامل من متجر موثوق ومُتحقق منه.'],
                    ],
                    [
                        'id'          => 'trust-2',
                        'icon_kind'   => 'shipping',
                        'title'       => ['en' => 'Fast & Free Shipping',  'ar' => 'شحن سريع ومجاني'],
                        'description' => ['en' => 'Free delivery across KSA, fast shipping, full tracking, and guaranteed on-time delivery.', 'ar' => 'توصيل مجاني في أنحاء المملكة، شحن سريع، تتبع كامل، وتسليم في الوقت المحدد.'],
                    ],
                    [
                        'id'          => 'trust-3',
                        'icon_kind'   => 'quality',
                        'title'       => ['en' => '100% Quality',          'ar' => 'جودة 100٪'],
                        'description' => ['en' => 'We guarantee handmade quality with premium materials, strict quality checks, and complete satisfaction.', 'ar' => 'نضمن جودة حِرَفية بمواد فاخرة وفحوصات صارمة ورضاك التام.'],
                    ],
                ],
                'newsletter' => [
                    'title'              => ['en' => 'Newsletters',              'ar' => 'النشرات البريدية'],
                    'email_placeholder'  => ['en' => 'Your email address...',    'ar' => 'بريدك الإلكتروني...'],
                    'submit_label'       => ['en' => 'Subscribe',                'ar' => 'اشترك'],
                    'disclaimer_intro'   => ['en' => 'Your personal data will be used to support your experience throughout this website, and for other purposes described in our ', 'ar' => 'ستُستخدم بياناتك الشخصية لدعم تجربتك على هذا الموقع ولأغراض أخرى موضحة في '],
                    'privacy_link_label' => ['en' => 'Privacy Policy',           'ar' => 'سياسة الخصوصية'],
                    'disclaimer_outro'   => ['en' => '.',                        'ar' => '.'],
                    'privacy_path'       => '/contact',
                ],
            ],

            'home_reviews_meta' => [
                'title'                => ['en' => 'What our customers say',    'ar' => 'ماذا يقول عملاؤنا'],
                'subtitle'             => ['en' => 'Trusted by watch enthusiasts worldwide', 'ar' => 'يثق بنا عشاق الساعات حول العالم'],
                'verified_buyer_label' => ['en' => 'Verified Buyer',            'ar' => 'مشتري موثق'],
                'rating_aria_label'    => ['en' => '5 out of 5 stars',          'ar' => '5 من 5 نجوم'],
            ],

            'home_faq_meta' => [
                'title' => ['en' => 'FAQ', 'ar' => 'الأسئلة الشائعة'],
            ],
        ];

        foreach ($settings as $key => $value) {
            StoreSetting::updateOrCreate(
                ['key' => $key],
                ['value' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)],
            );
        }
    }

    // -----------------------------------------------------------------------
    // Craft cards
    // -----------------------------------------------------------------------

    private function seedCraftCards(): void
    {
        HomeCraftCard::truncate();

        $cards = [
            [
                'title_en'       => 'Curated Italian Leather',
                'title_ar'       => 'جلد إيطالي منتقى بعناية',
                'description_en' => 'Every Vantier strap starts with Italian leather renowned for its richness, natural grain, and ability to age with character.',
                'description_ar' => 'تبدأ كل سوار من فانتييه بجلد إيطالي يُشتهر بثرائه وحبّته الطبيعية وقدرته على الاكتساب بمرور الوقت.',
                'image_url'      => 'https://thevantier.com/cdn/shop/files/ChatGPT_Image_Feb_20_2026_11_14_51_PM.png?v=1771611310&width=1024',
                'sort_order'     => 1,
            ],
            [
                'title_en'       => 'Cut With Purpose',
                'title_ar'       => 'قطعٌ بقصدٍ واضح',
                'description_en' => 'Each piece is shaped with precision, ensuring every strap feels balanced, refined, and worthy of the timepiece it carries.',
                'description_ar' => 'يُشكّل كل قطعة بدقة، لتشعر بأن كل سوار متوازن وراقٍ ويستحق الساعة التي يحملها.',
                'image_url'      => 'https://thevantier.com/cdn/shop/files/ChatGPT_Image_Feb_20_2026_07_44_56_PM.png?v=1771598773&width=1024',
                'sort_order'     => 2,
            ],
            [
                'title_en'       => 'Stitched with Mastery',
                'title_ar'       => 'خياطة ببراعة',
                'description_en' => 'Every strap is hand-stitched with precision, and centuries of leathercraft tradition meet modern refinement.',
                'description_ar' => 'يُخاط كل سوار يدوياً بدقة، حيث تلتقي قرون من تقاليد صناعة الجلد مع أناقة العصر.',
                'image_url'      => 'https://thevantier.com/cdn/shop/files/ChatGPT_Image_Feb_20_2026_07_50_34_PM.png?v=1771599071&width=1024',
                'sort_order'     => 3,
            ],
            [
                'title_en'       => 'The Signature Finish',
                'title_ar'       => 'اللمسة الأخيرة',
                'description_en' => 'From polished hardware to smooth edges, every strap is perfected to embody Vantier\'s uncompromising standard of luxury.',
                'description_ar' => 'من المعدّات المصقولة إلى الحواف الناعمة، يُنقّى كل سوار ليجسّد معيار فانتييه الرفيع في الفخامة.',
                'image_url'      => 'https://thevantier.com/cdn/shop/files/ChatGPT_Image_Feb_20_2026_07_55_45_PM.png?v=1771599362&width=1024',
                'sort_order'     => 4,
            ],
        ];

        HomeCraftCard::insert($cards);
    }

    // -----------------------------------------------------------------------
    // Workshop items
    // -----------------------------------------------------------------------

    private function seedWorkshopItems(): void
    {
        HomeWorkshopItem::truncate();

        $sizeOptions = [
            ['id' => '19-16',      'label' => ['en' => '19–16 mm',    'ar' => '١٩–١٦ مم']],
            ['id' => '20-18',      'label' => ['en' => '20–18 mm',    'ar' => '٢٠–١٨ مم']],
            ['id' => '20-16',      'label' => ['en' => '20–16 mm',    'ar' => '٢٠–١٦ مم']],
            ['id' => '21-18',      'label' => ['en' => '21–18 mm',    'ar' => '٢١–١٨ مم']],
            ['id' => '24-22',      'label' => ['en' => '24–22 mm',    'ar' => '٢٤–٢٢ مم']],
            ['id' => '22-20',      'label' => ['en' => '22–20 mm',    'ar' => '٢٢–٢٠ مم']],
            ['id' => 'apple-watch','label' => ['en' => 'Apple Watch', 'ar' => 'Apple Watch'], 'variant' => 'apple'],
        ];

        $items = [
            [
                'title_en'     => 'Terracotta Alcantara',
                'title_ar'     => 'ألكانتارا تراكوتا',
                'video_mp4'    => 'https://cdn.shopify.com/videos/c/o/v/7d1f0eae88b746e9a8594d855f300f25.mp4',
                'video_poster' => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=800&q=80&auto=format&fit=crop',
                'thumb_url'    => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=240&q=80&auto=format&fit=crop',
                'modal_gallery_urls' => [
                    'https://images.unsplash.com/photo-1524592094714-0f0654e20314?w=640&q=80&auto=format&fit=crop',
                    'https://images.unsplash.com/photo-1434056886845-dac89ffe9b56?w=640&q=80&auto=format&fit=crop',
                ],
                'modal_body_primary_html' => [
                    'en' => '<p><strong>Terracotta Alcantara</strong> balances warmth and restraint — a tactile suede grain that catches low light while staying quietly refined on the wrist.</p>',
                    'ar' => '<p><strong>ألكانتارا تراكوتا</strong> توازن بين الدفء والاعتدال — حبة سويدية لمسّها يخطف الضوء الخافت مع أناقة هادئة على المعصم.</p>',
                ],
                'modal_body_secondary_html' => [
                    'en' => '<p>Meticulously hand-finished, the strap features tonal stitching that follows its elegant taper.</p>',
                    'ar' => '<p>تمّ الإنهاء يدوياً بعناية؛ يبرز غرز بلون متناغم يتبع التدرّج الأنيق للسوار.</p>',
                ],
                'size_options'  => $sizeOptions,
                'current_price' => '395.00',
                'original_price'=> '465.00',
                'currency'      => '$',
                'product_path'  => '/shop',
                'sort_order'    => 1,
                'is_active'     => true,
            ],
            [
                'title_en'     => 'Midnight Ostrich',
                'title_ar'     => 'نعامي منتصف الليل',
                'video_mp4'    => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerEscapes.mp4',
                'video_poster' => 'https://images.unsplash.com/photo-1533139502658-0198f920d8e8?w=800&q=80&auto=format&fit=crop',
                'thumb_url'    => 'https://images.unsplash.com/photo-1533139502658-0198f920d8e8?w=240&q=80&auto=format&fit=crop',
                'modal_gallery_urls'        => [],
                'modal_body_primary_html'   => null,
                'modal_body_secondary_html' => null,
                'size_options'  => $sizeOptions,
                'current_price' => '415.00',
                'original_price'=> '495.00',
                'currency'      => '$',
                'product_path'  => '/shop',
                'sort_order'    => 2,
                'is_active'     => true,
            ],
            [
                'title_en'     => 'Taupe Epsom',
                'title_ar'     => 'إبسوم Taupe',
                'video_mp4'    => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ElephantsDream.mp4',
                'video_poster' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=800&q=80&auto=format&fit=crop',
                'thumb_url'    => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?w=240&q=80&auto=format&fit=crop',
                'modal_gallery_urls'        => [],
                'modal_body_primary_html'   => null,
                'modal_body_secondary_html' => null,
                'size_options'  => $sizeOptions,
                'current_price' => '389.00',
                'original_price'=> null,
                'currency'      => '$',
                'product_path'  => '/shop',
                'sort_order'    => 3,
                'is_active'     => true,
            ],
            [
                'title_en'     => 'Sapphire Teju',
                'title_ar'     => 'تيخو ياقوتي',
                'video_mp4'    => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4',
                'video_poster' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=80&auto=format&fit=crop',
                'thumb_url'    => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=240&q=80&auto=format&fit=crop',
                'modal_gallery_urls'        => [],
                'modal_body_primary_html'   => null,
                'modal_body_secondary_html' => null,
                'size_options'  => $sizeOptions,
                'current_price' => '498.00',
                'original_price'=> '575.00',
                'currency'      => '$',
                'product_path'  => '/shop',
                'sort_order'    => 4,
                'is_active'     => true,
            ],
            [
                'title_en'     => 'Vintage Cognac',
                'title_ar'     => 'كونياك عتيق',
                'video_mp4'    => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerFun.mp4',
                'video_poster' => 'https://images.unsplash.com/photo-1596944924616-7b38e7cfac36?w=800&q=80&auto=format&fit=crop',
                'thumb_url'    => 'https://images.unsplash.com/photo-1596944924616-7b38e7cfac36?w=240&q=80&auto=format&fit=crop',
                'modal_gallery_urls'        => [],
                'modal_body_primary_html'   => null,
                'modal_body_secondary_html' => null,
                'size_options'  => $sizeOptions,
                'current_price' => '402.00',
                'original_price'=> '478.00',
                'currency'      => '$',
                'product_path'  => '/shop',
                'sort_order'    => 5,
                'is_active'     => true,
            ],
        ];

        foreach ($items as $item) {
            HomeWorkshopItem::create($item);
        }
    }

    // -----------------------------------------------------------------------
    // Customer reviews
    // -----------------------------------------------------------------------

    private function seedCustomerReviews(): void
    {
        HomeCustomerReview::truncate();

        $reviews = [
            [
                'author_name'      => 'Saleh Alsultan',
                'review_en'        => 'Totally satisfied, packing was amazing <3',
                'review_ar'        => 'راضٍ تماماً، التغليف كان رائعاً <3',
                'product_image_url'=> 'https://thevantier.com/cdn/shop/files/strap-image-1_7d5a97e9-3ddc-49ce-b246-89c69cc14573.png',
                'product_name_en'  => 'Bordeaux Alligator',
                'product_name_ar'  => 'تمساح بوردو',
                'current_price'    => '395.00',
                'compare_at_price' => '465.00',
                'currency_en'      => 'SAR ',
                'currency_ar'      => 'ر.س ',
                'sort_order'       => 1,
                'is_active'        => true,
            ],
            [
                'author_name'      => 'محمد القرني',
                'review_en'        => 'Exceptional quality and attention to detail — the strap feels premium in every way.',
                'review_ar'        => 'جودة استثنائية واهتمام بالتفاصيل؛ السوار يشعر بالفخامة في كل تفصيل.',
                'product_image_url'=> 'https://thevantier.com/cdn/shop/files/strap-image-1_7d5a97e9-3ddc-49ce-b246-89c69cc14573.png',
                'product_name_en'  => 'Golden Tan Original Ostrich Leather Strap',
                'product_name_ar'  => 'سوار نعامي ذهبي تان أصلي',
                'current_price'    => '470.00',
                'compare_at_price' => '550.00',
                'currency_en'      => 'SAR ',
                'currency_ar'      => 'ر.س ',
                'sort_order'       => 2,
                'is_active'        => true,
            ],
            [
                'author_name'      => 'أحمد الراشد',
                'review_en'        => 'Outstanding craftsmanship and materials — worth every riyal.',
                'review_ar'        => 'صنعة ومواد رائعة — تستحق كل ريال.',
                'product_image_url'=> 'https://thevantier.com/cdn/shop/files/strap-image-1_7d5a97e9-3ddc-49ce-b246-89c69cc14573.png',
                'product_name_en'  => 'Aegean Python',
                'product_name_ar'  => 'بايثون بحر إيجه',
                'current_price'    => '395.00',
                'compare_at_price' => '465.00',
                'currency_en'      => 'SAR ',
                'currency_ar'      => 'ر.س ',
                'sort_order'       => 3,
                'is_active'        => true,
            ],
        ];

        HomeCustomerReview::insert($reviews);
    }

    // -----------------------------------------------------------------------
    // FAQs
    // -----------------------------------------------------------------------

    private function seedFaqs(): void
    {
        HomeFaq::truncate();

        $faqs = [
            [
                'question_en' => 'How do I know what size strap fits my watch?',
                'question_ar' => 'كيف أعرف مقاس السوار المناسب لساعتي؟',
                'answer_en'   => 'The size depends on the lug width of your watch — the distance between the two metal bars where the strap attaches. Most watches use standard sizes like 20mm or 22mm.',
                'answer_ar'   => 'يعتمد المقاس على عرض الأذنين (lug width) — المسافة بين شريطي المعدن حيث يثبت السوار. كثير من الساعات تستخدم مقاسات قياسية مثل 20مم أو 22مم.',
                'sort_order'  => 1, 'is_active' => true,
            ],
            [
                'question_en' => 'Do these straps fit the Apple Watch?',
                'question_ar' => 'هل تناسب هذه الأسوار ساعة آبل؟',
                'answer_en'   => 'Yes — we offer straps made specifically for Apple Watch with the correct lug adapters. Choose your Apple Watch case size and generation when ordering.',
                'answer_ar'   => 'نعم — لدينا أساور مصممة لساعة آبل مع وصلات الأذن المناسبة. اختر مقاس العلبة والجيل عند الطلب.',
                'sort_order'  => 2, 'is_active' => true,
            ],
            [
                'question_en' => 'What kind of leather does Vantier use?',
                'question_ar' => 'ما نوع الجلد الذي تستخدمه فانتييه؟',
                'answer_en'   => 'Vantier uses premium Italian and European leathers — including calf, alligator, ostrich, and other curated hides — selected for grain, durability, and how they age over time.',
                'answer_ar'   => 'تستخدم فانتييه جلوداً إيطالية وأوروبية فاخرة — منها العجل والتمساح والنعام وغيرها — تُختار بحبّة الجلد ومتانتها وجمال شيخوختها.',
                'sort_order'  => 3, 'is_active' => true,
            ],
            [
                'question_en' => 'Will the color of my strap look exactly like the photo?',
                'question_ar' => 'هل سيكون لون السوار مطابقاً تماماً للصورة؟',
                'answer_en'   => 'Natural leather varies slightly from hide to hide. We photograph in accurate lighting, but minor tone differences are normal and part of the material\'s character.',
                'answer_ar'   => 'الجلد الطبيعي يختلف قليلاً بين القطع. نصوّر بإضاءة دقيقة، لكن اختلافات طفيفة في اللون طبيعية وهي جزء من طابع الجلد.',
                'sort_order'  => 4, 'is_active' => true,
            ],
            [
                'question_en' => 'Do the straps come with buckles?',
                'question_ar' => 'هل تأتي الأسوار مع إبزيم؟',
                'answer_en'   => 'Many straps include a buckle where listed; some are sold strap-only so you can reuse your existing hardware. Check the product page for what is included.',
                'answer_ar'   => 'كثير من الأسوار يُرفق بها إبزيم حيث يُذكر؛ وبعضها يُباع السوار فقط لاستخدام إبزيمك الحالي. راجع صفحة المنتج لما يُشحن مع القطعة.',
                'sort_order'  => 5, 'is_active' => true,
            ],
            [
                'question_en' => 'What are the delivery times within Saudi Arabia?',
                'question_ar' => 'ما مدة التوصيل داخل السعودية؟',
                'answer_en'   => 'Typical delivery within Saudi Arabia is a few business days depending on your city and courier. You will receive tracking once your order ships.',
                'answer_ar'   => 'غالباً يستغرق التوصيل داخل المملكة عدة أيام عمل حسب المدينة والشحن. ستصلك رسالة تتبع عند شحن الطلب.',
                'sort_order'  => 6, 'is_active' => true,
            ],
        ];

        HomeFaq::insert($faqs);
    }
}
