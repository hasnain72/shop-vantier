<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Blog;
use Illuminate\Database\Seeder;

class VantierJournalSeeder extends Seeder
{
    public function run(): void
    {
        $blog = Blog::firstOrCreate(
            ['slug' => 'vantier-journal'],
            [
                'title'    => 'Vantier Journal',
                'title_ar' => 'مجلة فانتير',
                'slug'     => 'vantier-journal',
            ]
        );

        $articles = [
            [
                'title'        => 'The Anatomy of a Premium Watch Strap',
                'title_ar'     => 'تشريح حزام الساعة الفاخر',
                'slug'         => 'premium-watch-strap-anatomy',
                'author'       => 'Tariq Hassan',
                'author_ar'    => 'طارق حسان',
                'image'        => 'https://images.unsplash.com/photo-1617038260897-41a1f14a8ca0?auto=format&fit=crop&w=1400&q=80',
                'published'    => true,
                'published_at' => now()->subDays(10),
                'summary_html' => '<p>What separates a premium strap from an ordinary one? We break down every layer — from the top grain down to the lining stitch.</p>',
                'summary_html_ar' => '<p>ما الذي يميز الحزام الفاخر عن العادي؟ نحلل كل طبقة من الجلد الخارجي إلى خيوط البطانة.</p>',
                'body_html'    => '<p>A watch strap is far more than a band that holds your timepiece to your wrist. The finest examples are miniature works of leather craft, built with as much care as the movement they carry.</p>

<h2>The Top Grain</h2>
<p>The outer face of a strap defines its character. Calfskin offers a refined, smooth surface that develops a natural shine over time. Crocodile and alligator leathers are prized for their distinctive scale patterns, each unique to the individual hide. Suede and nubuck present a softer, more casual face.</p>

<h2>The Core</h2>
<p>Between the outer leather and the lining sits the padding layer — typically foam or additional leather — which gives the strap its body and cushioning. A well-padded strap sits comfortably against the wrist and holds its shape for years.</p>

<h2>The Lining</h2>
<p>Often overlooked, the lining is the layer your skin actually touches. Suede lining is soft and breathable; leather lining is more durable. The best straps use a lining that resists moisture and prevents the strap from stiffening with wear.</p>

<h2>The Stitching</h2>
<p>Hand stitching, done with a saddle-stitch technique using two needles, is the gold standard. It is stronger than machine stitching — if one thread breaks, the seam holds. Look for tight, even stitches in waxed linen or nylon thread.</p>

<h2>The Hardware</h2>
<p>A polished buckle in solid stainless steel or solid gold completes the strap. Plated hardware will chip and tarnish; solid metal hardware is a lifetime investment. Deployant clasps offer added security and reduce wear on the leather at the fold point.</p>

<p>Every element of a Vantier strap is selected for longevity. We use only full-grain leathers, hand-stitched seams, and brushed stainless steel hardware — because the details matter.</p>',
                'body_html_ar' => '<p>حزام الساعة أكثر بكثير من مجرد شريط يثبت ساعتك في معصمك. أجود الأحزمة هي تحف جلدية مصغرة، تُصنع بنفس العناية التي تُصنع بها الحركات التي تحملها.</p>

<h2>الجلد الخارجي</h2>
<p>الوجه الخارجي للحزام هو ما يحدد طابعه. يوفر جلد العجل سطحاً ناعماً ومشذباً يكتسب لمعاناً طبيعياً مع الوقت. تتميز جلود التمساح والأفعى بأنماط حراشفها الفريدة لكل قطعة. أما الجلد المدبوغ والنوباك فيمنحان مظهراً أكثر نعومة وعفوية.</p>

<h2>القلب الداخلي</h2>
<p>بين الجلد الخارجي والبطانة توجد طبقة الحشو، وعادةً ما تكون من الإسفنج أو الجلد الإضافي، وهي ما يمنح الحزام قوامه ومرونته. يوفر الحزام المحشو بشكل جيد راحة في الارتداء ويحافظ على شكله لسنوات.</p>

<h2>البطانة</h2>
<p>كثيراً ما تُغفل البطانة رغم أنها الطبقة التي تلامس جلدك فعلياً. البطانة المصنوعة من الجلد المدبوغ ناعمة ومريحة؛ أما جلد البطانة فهو أكثر متانة. أجود الأحزمة تستخدم بطانة مقاومة للرطوبة تمنع الحزام من التصلب.</p>

<h2>الخياطة</h2>
<p>الخياطة اليدوية بأسلوب السرج بإبرتين هي المعيار الذهبي — فهي أمتن من الخياطة الآلية، لأنه حتى لو انقطع خيط واحد يبقى الدرز متماسكاً. ابحث عن غرز متساوية ومحكمة بخيوط كتان أو نايلون مشمّعة.</p>

<h2>الأجهزة المعدنية</h2>
<p>الإبزيم المصقول من الفولاذ المقاوم للصدأ الصلب أو الذهب الخالص يُكمل الحزام. الطلاء المعدني يتقشر ويصدأ؛ أما المعدن الصلب فهو استثمار مدى الحياة. تمنح مشابك النشر أماناً إضافياً وتقلل التآكل عند نقطة الطي.</p>

<p>كل عنصر في حزام Vantier مختار للمتانة. نستخدم فقط جلوداً كاملة الحبوب، وخياطة يدوية، وأجهزة فولاذية مصقولة — لأن التفاصيل هي التي تصنع الفارق.</p>',
                'tags'         => ['leather', 'craftsmanship', 'guide', 'anatomy'],
            ],
            [
                'title'        => 'How Leather Ages: The Beauty of Patina',
                'title_ar'     => 'كيف يتقدم الجلد في العمر: جمال البتينا',
                'slug'         => 'leather-aging-patina',
                'author'       => 'Aisha Siddiqui',
                'author_ar'    => 'عائشة صديقي',
                'image'        => 'https://images.unsplash.com/photo-1473186505569-9c61870c11f9?auto=format&fit=crop&w=1400&q=80',
                'published'    => true,
                'published_at' => now()->subDays(4),
                'summary_html' => '<p>Unlike synthetic materials, full-grain leather improves with age. Here is what to expect in the first year — and beyond.</p>',
                'summary_html_ar' => '<p>على عكس المواد الاصطناعية، يتحسن الجلد الكامل الحبوب مع مرور الوقت. إليك ما يمكن توقعه في السنة الأولى وما بعدها.</p>',
                'body_html'    => '<p>Patina is the gradual darkening, softening, and character that develops on leather over time. It is, for leather enthusiasts, the whole point — proof that the material is alive and responding to the world around it.</p>

<h2>What Causes Patina</h2>
<p>Natural oils from your skin, UV light exposure, and the mechanical flex of wearing all contribute to patina. The leather absorbs oils, which darken and enrich the colour. Areas of frequent flex — at the fold near the buckle — develop creases that catch light differently, adding depth.</p>

<h2>The First Three Months</h2>
<p>A new strap begins stiff and slightly resistant. The surface colour is even and bright. Within the first few weeks, you will notice the leather softening at the contact points. The areas under the buckle and around the keepers begin to show the first signs of wear — a slight lightening where the hardware rubs.</p>

<h2>Six to Twelve Months</h2>
<p>By this point, the strap has moulded itself to your wrist. The fold crease is deep and comfortable. Colour variation between sun-exposed areas and the underside becomes visible. The leather surface begins to take on a subtle sheen — the early stages of patina.</p>

<h2>Beyond the First Year</h2>
<p>A well-cared-for leather strap at two or three years looks entirely different from when new — richer in colour, softer in hand, with a surface story only your wrist could write. This is why collectors keep old straps: each one is unique.</p>

<h2>Encouraging Good Patina</h2>
<ul>
  <li>Condition with a light leather balm every few months</li>
  <li>Rotate your straps — a rested strap ages more evenly</li>
  <li>Avoid soaking in water; light moisture is fine</li>
  <li>Store flat or on a strap roll, not folded</li>
</ul>

<p>The finest patina stories belong to Horween shell cordovan and vegetable-tanned calfskin. Both are available in the Vantier Heirloom collection — built to be worn for decades.</p>',
                'body_html_ar' => '<p>البتينا هي الاداكن التدريجي والنعومة والطابع الخاص الذي يتطور على الجلد مع مرور الوقت. وبالنسبة لعشاق الجلود، هذا هو الهدف كله — دليل على أن المادة حية وتتفاعل مع العالم من حولها.</p>

<h2>ما الذي يسبب البتينا</h2>
<p>الزيوت الطبيعية من جلدك، والتعرض للأشعة فوق البنفسجية، والمرونة الميكانيكية لحركة الارتداء — كلها تساهم في البتينا. يمتص الجلد هذه الزيوت مما يجعل لونه أغمق وأكثر ثراءً. تُطوّر المناطق كثيرة الطي — عند الإبزيم — تجاعيد تعكس الضوء بشكل مختلف مضيفةً عمقاً جمالياً.</p>

<h2>الأشهر الثلاثة الأولى</h2>
<p>يبدأ الحزام الجديد صلباً ومتيناً واللون موحداً ومشرقاً. في غضون أسابيع قليلة ستلاحظ ليونة الجلد عند نقاط التماس. تبدأ المناطق تحت الإبزيم وحول الحلقات بإظهار أولى علامات التآكل — احتكاك خفيف من الأجهزة المعدنية.</p>

<h2>من ستة إلى اثني عشر شهراً</h2>
<p>في هذه المرحلة شكّل الحزام نفسه وفق معصمك. تجعّد الطي عميق ومريح. يظهر تباين لوني بين المناطق المعرضة للشمس والجانب السفلي. يبدأ سطح الجلد باكتساب بريق خفيف — المرحلة الأولى من البتينا.</p>

<h2>ما بعد السنة الأولى</h2>
<p>يبدو الحزام الجلدي المُعتنى به جيداً بعد سنتين أو ثلاث مختلفاً تماماً عن لحظة شرائه — أغنى في اللون وأكثر نعومة وعلى سطحه قصة لا يستطيع كتابتها سوى معصمك. لهذا يحتفظ المقتنون بأحزمتهم القديمة — كل واحد فريد من نوعه.</p>

<h2>تشجيع البتينا الجيدة</h2>
<ul>
  <li>رطّب الجلد ببلسم خفيف كل بضعة أشهر</li>
  <li>دوّر أحزمتك — الحزام المستريح يتقدم في العمر بشكل أكثر انتظاماً</li>
  <li>تجنب النقع في الماء؛ الرطوبة الخفيفة لا بأس بها</li>
  <li>احفظه مسطحاً أو على لفافة الأحزمة وليس مطوياً</li>
</ul>

<p>أجمل قصص البتينا تنتمي لجلد هورين شيل كوردوان والجلد العجلي المدبوغ نباتياً. كلاهما متوفران في مجموعة Vantier Heirloom — مصنوعة لتُرتدى لعقود.</p>',
                'tags'         => ['leather', 'patina', 'care', 'aging'],
            ],
            [
                'title'        => 'Choosing the Right Strap Width for Your Watch',
                'title_ar'     => 'اختيار عرض الحزام المناسب لساعتك',
                'slug'         => 'choosing-right-strap-width',
                'author'       => 'Tariq Hassan',
                'author_ar'    => 'طارق حسان',
                'image'        => 'https://images.unsplash.com/photo-1524592094714-0f0654e20314?auto=format&fit=crop&w=1400&q=80',
                'published'    => true,
                'published_at' => now()->subDays(17),
                'summary_html' => '<p>Strap width affects how your watch looks and feels. Here is the simple guide to measuring your lug width and finding the perfect fit.</p>',
                'summary_html_ar' => '<p>عرض الحزام يؤثر على مظهر ساعتك وملمسها. إليك الدليل البسيط لقياس عرض اللوق وإيجاد المقاس المثالي.</p>',
                'body_html'    => '<p>Getting the strap width right is the single most important step in finding a strap that looks good on your watch. A strap even 1mm too wide will overhang the lugs; too narrow and you will see an uncomfortable gap.</p>

<h2>What is Lug Width?</h2>
<p>The lug width (also called strap width) is the distance between the two lugs — the protruding arms at the top and bottom of the watch case where the strap attaches. This measurement is always given in millimetres.</p>

<h2>How to Measure</h2>
<p>You need a mm ruler or digital calipers. Measure the gap between the inner faces of the lugs at the point where the spring bar sits. Common sizes: 18mm, 19mm, 20mm, 21mm, 22mm. The vast majority of watches use 20mm or 22mm.</p>

<h2>Standard Sizing Guide</h2>
<ul>
  <li><strong>16mm:</strong> Small ladies watches, slim vintage pieces</li>
  <li><strong>18mm:</strong> Smaller dress watches, vintage Rolex Oyster, Cartier Santos</li>
  <li><strong>19mm:</strong> Some Rolex Datejust models, vintage sport watches</li>
  <li><strong>20mm:</strong> The universal standard — Rolex Submariner, Omega Seamaster, most sport watches</li>
  <li><strong>21mm:</strong> Some Panerai models</li>
  <li><strong>22mm:</strong> Larger pilot watches, Panerai, IWC Big Pilot, Breitling Navitimer</li>
  <li><strong>24mm:</strong> Oversized pilot and tool watches</li>
</ul>

<h2>Tapered Straps</h2>
<p>Many straps taper — they are wider at the lug end and narrower at the buckle end. A 20/16 strap fits 20mm lugs and closes with a 16mm buckle. This is classic proportioning and looks elegant on dress watches.</p>

<h2>Straight-End vs Curved-End</h2>
<p>Some watches — notably Rolex — have slightly curved lugs. A straight-end strap will fit but may leave a small gap. Curved-end straps are cut to follow the lug curve exactly for a flush fit. Check your specific model before ordering.</p>',
                'body_html_ar' => '<p>اختيار عرض الحزام الصحيح هو أهم خطوة في إيجاد حزام يبدو جيداً على ساعتك. حزام أعرض بمليمتر واحد فقط سيتجاوز اللوقات؛ وإذا كان أضيق فستظهر فجوة مزعجة.</p>

<h2>ما هو عرض اللوق؟</h2>
<p>عرض اللوق (يُسمى أيضاً عرض الحزام) هو المسافة بين اللوقين — الذراعين البارزتين في أعلى وأسفل علبة الساعة حيث يتصل الحزام. يُقاس دائماً بالمليمترات.</p>

<h2>كيفية القياس</h2>
<p>تحتاج إلى مسطرة بالمليمترات أو كاليبر رقمي. قس الفجوة بين الوجهين الداخليين للوقتين عند نقطة جلوس الربيع. المقاسات الشائعة: 18، 19، 20، 21، 22 مم. معظم الساعات تستخدم 20 أو 22 مم.</p>

<h2>دليل المقاسات القياسية</h2>
<ul>
  <li><strong>16مم:</strong> ساعات نسائية صغيرة، قطع عتيقة رفيعة</li>
  <li><strong>18مم:</strong> ساعات رسمية أصغر، رولكس أويستر العتيقة، كارتييه سانتوس</li>
  <li><strong>19مم:</strong> بعض موديلات رولكس داتجست، ساعات رياضية عتيقة</li>
  <li><strong>20مم:</strong> المعيار العالمي — رولكس سابمارينر، أوميغا سيماستر، معظم الساعات الرياضية</li>
  <li><strong>21مم:</strong> بعض موديلات بانيراي</li>
  <li><strong>22مم:</strong> ساعات الطيار الكبيرة، بانيراي، IWC بيغ بايلوت، بريتلينغ نافيتيمر</li>
  <li><strong>24مم:</strong> ساعات الطيار والأدوات الكبيرة الحجم</li>
</ul>

<h2>الأحزمة المدببة</h2>
<p>كثير من الأحزمة مدببة — أعرض عند طرف اللوق وأضيق عند الإبزيم. حزام 20/16 يناسب لوقات 20 مم ويُغلق بإبزيم 16 مم. هذه نسب كلاسيكية وتبدو أنيقة على ساعات الأزياء.</p>

<h2>الطرف المستقيم مقابل الطرف المنحني</h2>
<p>بعض الساعات — رولكس تحديداً — لها لوقات منحنية قليلاً. الحزام ذو الطرف المستقيم يناسبها لكن قد تترك فجوة صغيرة. الأحزمة ذات الطرف المنحني مقصوصة لتتبع انحناء اللوق بدقة. تحقق من موديلك تحديداً قبل الطلب.</p>',
                'tags'         => ['straps', 'sizing', 'guide', 'lug-width'],
            ],
        ];

        foreach ($articles as $data) {
            Article::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, ['blog_id' => $blog->id])
            );
        }
    }
}
