<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Blog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // ── Blog 1: Watch Reviews ──────────────────────────────────────────────────
        $reviewsBlog = Blog::firstOrCreate(
            ['slug' => 'watch-reviews'],
            [
                'title'       => 'Watch Reviews',
                'commentable' => 'no',
                'meta_title'       => 'Watch Reviews – Expert Watch Guides',
                'meta_description' => 'In-depth reviews and guides for watch enthusiasts.',
            ]
        );

        $reviewArticles = [
            [
                'title'    => 'The Best Leather Watch Straps of 2026',
                'slug'     => 'best-leather-watch-straps-2026',
                'summary_html' => '<p>We tested over 20 leather straps across different price points. Here are our top picks for style, durability, and comfort.</p>',
                'body_html'=> <<<HTML
<p>A great leather strap can transform any watch. Whether you're dressing up a dress watch or adding character to a sport piece, the right leather strap makes all the difference.</p>

<h2>What to Look for in a Leather Strap</h2>
<ul>
  <li><strong>Leather type:</strong> Calfskin offers a balance of softness and durability. Crocodile embossed is a budget-friendly luxury look.</li>
  <li><strong>Lining:</strong> Suede-lined straps are gentler on the wrist but show wear faster.</li>
  <li><strong>Stitching:</strong> Contrast stitching adds character; matching stitching looks cleaner.</li>
  <li><strong>Buckle:</strong> Stainless steel tang buckles are classic; deployant clasps are more secure.</li>
</ul>

<h2>Our Top Picks</h2>
<p>After months of testing, our Classic Leather Watch strap series stands out for its genuine calfskin construction and hand-stitched edges at an accessible price point.</p>

<p>We also love the Horween leather options for a more rustic, aged look — perfect for field and pilot watches.</p>

<h2>Care Tips</h2>
<p>Keep leather away from water and prolonged sunlight. A small amount of leather conditioner every 3–6 months will extend life significantly. Never wear the same leather strap two days in a row — let it air out.</p>
HTML,
                'tags'      => ['leather', 'straps', 'guide', 'review'],
                'published' => true,
                'published_at' => now()->subDays(15),
                'author'    => 'Tariq Hassan',
            ],
            [
                'title'    => 'NATO Straps: A Complete Buyer\'s Guide',
                'slug'     => 'nato-straps-buyers-guide',
                'summary_html' => '<p>NATO straps are affordable, durable, and incredibly versatile. Here\'s everything you need to know before buying.</p>',
                'body_html'=> <<<HTML
<p>Originally designed for British military use in the 1970s, the NATO strap has become one of the most popular watch accessories in the world. And for good reason — they're tough, comfortable, and look great on almost any watch.</p>

<h2>NATO vs Zulu</h2>
<p>Both share a similar design but differ in hardware. NATO straps typically have thinner rings and a more casual look. Zulu straps use larger, more robust rings suited for tool watches.</p>

<h2>Width Guide</h2>
<ul>
  <li><strong>18mm:</strong> Smaller dress watches, vintage pieces</li>
  <li><strong>20mm:</strong> The universal standard — fits most watches</li>
  <li><strong>22mm:</strong> Larger sport and dive watches</li>
  <li><strong>24mm:</strong> Big tool watches, pilot watches</li>
</ul>

<h2>Material Matters</h2>
<p>Military-grade nylon is the gold standard. Look for tightly woven nylon with stainless steel hardware — avoid painted buckles that chip over time. Our NATO Nylon Strap uses ballistic-grade nylon with brushed stainless steel keepers.</p>

<h2>How to Install</h2>
<p>Remove the spring bars from your watch. Thread the NATO strap through the spring bar slot between the case and strap — no tools needed for most NATOs.</p>
HTML,
                'tags'      => ['nato', 'straps', 'guide', 'buyer-guide'],
                'published' => true,
                'published_at' => now()->subDays(8),
                'author'    => 'Tariq Hassan',
            ],
            [
                'title'    => 'How to Care for Your Watch: The Essential Guide',
                'slug'     => 'how-to-care-for-your-watch',
                'summary_html' => '<p>Proper maintenance extends the life of your watch for decades. Learn the basics of cleaning, servicing, and storage.</p>',
                'body_html'=> <<<HTML
<p>A quality watch is an investment. With the right care, it can last a lifetime and beyond. Here's what every watch owner should know.</p>

<h2>Daily Care</h2>
<ul>
  <li>Wipe your watch with a soft microfibre cloth after wearing to remove oils and sweat</li>
  <li>Avoid extreme temperature changes (don't leave watches in a hot car)</li>
  <li>Keep magnets away — they can affect movement accuracy</li>
</ul>

<h2>Cleaning Your Watch</h2>
<p>For metal bracelets, a soft toothbrush with mild soap and warm water works well. Rinse with clean water and dry thoroughly. For leather straps, use only a dry cloth — never submerge.</p>

<h2>Water Resistance</h2>
<p>Check your watch's water resistance rating before exposing it to water. 30m/3ATM means splash-proof only. 100m/10ATM is suitable for swimming. Diving watches are rated at 200m+.</p>

<h2>Storage</h2>
<p>Store watches in a dedicated watch box to protect from dust, light, and humidity. For automatic watches you don't wear daily, a watch winder keeps the movement running and prevents lubricants from drying out.</p>

<h2>Servicing</h2>
<p>Most mechanical watches need servicing every 5–7 years. Signs it's time: noticeably gaining or losing time, condensation under the crystal, or the crown feels gritty.</p>
HTML,
                'tags'      => ['care', 'maintenance', 'tips', 'guide'],
                'published' => true,
                'published_at' => now()->subDays(3),
                'author'    => 'Aisha Siddiqui',
            ],
        ];

        foreach ($reviewArticles as $articleData) {
            Article::firstOrCreate(
                ['slug' => $articleData['slug'], 'blog_id' => $reviewsBlog->id],
                array_merge($articleData, ['blog_id' => $reviewsBlog->id])
            );
        }

        // ── Blog 2: News & Updates ─────────────────────────────────────────────────
        $newsBlog = Blog::firstOrCreate(
            ['slug' => 'news'],
            [
                'title'            => 'News & Updates',
                'commentable'      => 'no',
                'meta_title'       => 'News – Watch Accessories Store',
                'meta_description' => 'Latest news, new arrivals, and updates from Watch Accessories Store.',
            ]
        );

        $newsArticles = [
            [
                'title'       => 'New Arrivals: Milanese Mesh Straps Now in Stock',
                'slug'        => 'milanese-mesh-straps-new-arrival',
                'summary_html'=> '<p>Our new Milanese Mesh Strap collection has arrived — available in Silver, Gold, Rose Gold, and Black in three widths.</p>',
                'body_html'   => <<<HTML
<p>We're excited to announce that our Milanese Mesh Strap range is now available in the store. After months of sourcing the perfect supplier, we've found a manufacturer that meets our quality standards at a price that won't break the bank.</p>

<h2>What's New</h2>
<ul>
  <li>4 finish options: Silver, Gold, Rose Gold, Black</li>
  <li>3 widths: 18mm, 20mm, 22mm</li>
  <li>Magnetic clasp — no tools required to adjust fit</li>
  <li>Solid stainless steel construction</li>
</ul>

<p>Grab yours while stocks last — we've seen high demand for the Rose Gold variant in particular.</p>
HTML,
                'tags'        => ['new-arrival', 'milanese', 'mesh', 'strap'],
                'published'   => true,
                'published_at'=> now()->subDays(10),
                'author'      => 'Admin',
            ],
            [
                'title'       => 'Free Shipping Extended: Now on All Orders Over $50',
                'slug'        => 'free-shipping-extended-50-usd',
                'summary_html'=> '<p>We\'ve permanently lowered our free shipping threshold from $75 to $50 on all international orders.</p>',
                'body_html'   => <<<HTML
<p>Good news for our international customers! We've permanently reduced our free international shipping threshold from $75 to $50.</p>

<p>This applies to all orders shipped to USA, UK, UAE, Canada, and the EU. Pakistani customers continue to enjoy free shipping on orders over PKR 5,000.</p>

<h2>How It Works</h2>
<p>Add items to your cart. When your order total reaches $50 (before tax), free standard shipping is automatically applied at checkout. No coupon code needed.</p>

<p>Thank you to everyone who has supported us — this change is a direct result of your loyalty.</p>
HTML,
                'tags'        => ['shipping', 'announcement', 'free-shipping'],
                'published'   => true,
                'published_at'=> now()->subDays(20),
                'author'      => 'Admin',
            ],
        ];

        foreach ($newsArticles as $articleData) {
            Article::firstOrCreate(
                ['slug' => $articleData['slug'], 'blog_id' => $newsBlog->id],
                array_merge($articleData, ['blog_id' => $newsBlog->id])
            );
        }
    }
}
