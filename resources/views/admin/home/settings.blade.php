@extends('admin.layouts.app')
@section('title', 'Home Page')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <div class="h4 mb-0">Home Page</div>
</div>

{{-- Tab nav --}}
<ul class="nav nav-tabs mb-4">
  <li class="nav-item"><a class="nav-link active" href="{{ route('admin.home.settings') }}">Settings</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.craft-cards') }}">Craft Cards</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.workshop') }}">Workshop Items</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.reviews') }}">Reviews</a></li>
  <li class="nav-item"><a class="nav-link" href="{{ route('admin.home.faqs') }}">FAQs</a></li>
</ul>

@include('admin.partials.alert')

<form method="POST" action="{{ route('admin.home.settings.update') }}">
@csrf @method('PUT')

{{-- Accordion sections --}}
<div class="accordion" id="homeAccordion">

  {{-- ── Hero Video ──────────────────────────────────────────────────── --}}
  <div class="accordion-item border-0 shadow-sm mb-3 rounded">
    <h2 class="accordion-header">
      <button class="accordion-button rounded" type="button" data-bs-toggle="collapse" data-bs-target="#sec-hero">
        <i class="fa-solid fa-film me-2 text-primary"></i> Hero Video
      </button>
    </h2>
    <div id="sec-hero" class="accordion-collapse collapse show">
      <div class="accordion-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">MP4 Video URL</label>
          <input name="hero_mp4_url" class="form-control" value="{{ $hero['mp4_url'] ?? '' }}" placeholder="https://...mp4">
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Poster Image URL <span class="text-muted fw-normal">(optional)</span></label>
          <input name="hero_poster" class="form-control" value="{{ $hero['poster'] ?? '' }}" placeholder="https://...jpg">
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Heading (English)</label>
            <input name="hero_heading_en" class="form-control" value="{{ $hero['heading']['en'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Heading (Arabic)</label>
            <input name="hero_heading_ar" class="form-control" dir="rtl" value="{{ $hero['heading']['ar'] ?? '' }}">
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── New Arrivals Meta ───────────────────────────────────────────── --}}
  <div class="accordion-item border-0 shadow-sm mb-3 rounded">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#sec-na">
        <i class="fa-solid fa-star me-2 text-warning"></i> New Arrivals
      </button>
    </h2>
    <div id="sec-na" class="accordion-collapse collapse">
      <div class="accordion-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Products to show</label>
          <input type="number" name="na_count" class="form-control" value="{{ $newArrivalsMeta['count'] ?? 5 }}" min="1" max="20" style="max-width:120px">
          <div class="form-text">Latest N active products from the database.</div>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Section Title (EN)</label>
            <input name="na_title_en" class="form-control" value="{{ $newArrivalsMeta['title']['en'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Section Title (AR)</label>
            <input name="na_title_ar" class="form-control" dir="rtl" value="{{ $newArrivalsMeta['title']['ar'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Subtitle (EN)</label>
            <input name="na_subtitle_en" class="form-control" value="{{ $newArrivalsMeta['subtitle']['en'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Subtitle (AR)</label>
            <input name="na_subtitle_ar" class="form-control" dir="rtl" value="{{ $newArrivalsMeta['subtitle']['ar'] ?? '' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">"View All" Label (EN)</label>
            <input name="na_view_all_en" class="form-control" value="{{ $newArrivalsMeta['view_all_label']['en'] ?? 'View All' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">"View All" Label (AR)</label>
            <input name="na_view_all_ar" class="form-control" dir="rtl" value="{{ $newArrivalsMeta['view_all_label']['ar'] ?? 'عرض الكل' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">"View All" Path</label>
            <input name="na_view_all_path" class="form-control" value="{{ $newArrivalsMeta['view_all_path'] ?? '/shop' }}">
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Featured Product ────────────────────────────────────────────── --}}
  <div class="accordion-item border-0 shadow-sm mb-3 rounded">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#sec-fp">
        <i class="fa-solid fa-crown me-2 text-success"></i> Featured Product
      </button>
    </h2>
    <div id="sec-fp" class="accordion-collapse collapse">
      <div class="accordion-body">
        <div class="mb-3">
          <label class="form-label fw-semibold">Product Image URL</label>
          <input name="fp_image_url" class="form-control" value="{{ $featuredProduct['image_url'] ?? '' }}">
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold">Eyebrow (EN)</label>
            <input name="fp_eyebrow_en" class="form-control" value="{{ $featuredProduct['eyebrow']['en'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Eyebrow (AR)</label>
            <input name="fp_eyebrow_ar" class="form-control" dir="rtl" value="{{ $featuredProduct['eyebrow']['ar'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Title (EN)</label>
            <input name="fp_title_en" class="form-control" value="{{ $featuredProduct['title']['en'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Title (AR)</label>
            <input name="fp_title_ar" class="form-control" dir="rtl" value="{{ $featuredProduct['title']['ar'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Body Text (EN)</label>
            <textarea name="fp_body_en" class="form-control" rows="3">{{ $featuredProduct['body']['en'] ?? '' }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Body Text (AR)</label>
            <textarea name="fp_body_ar" class="form-control" dir="rtl" rows="3">{{ $featuredProduct['body']['ar'] ?? '' }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Image Alt (EN)</label>
            <input name="fp_image_alt_en" class="form-control" value="{{ $featuredProduct['image_alt']['en'] ?? '' }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold">Image Alt (AR)</label>
            <input name="fp_image_alt_ar" class="form-control" dir="rtl" value="{{ $featuredProduct['image_alt']['ar'] ?? '' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Link Path</label>
            <input name="fp_link_path" class="form-control" value="{{ $featuredProduct['link_path'] ?? '/shop' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Link Aria Label (EN)</label>
            <input name="fp_link_aria_en" class="form-control" value="{{ $featuredProduct['link_aria_label']['en'] ?? '' }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Link Aria Label (AR)</label>
            <input name="fp_link_aria_ar" class="form-control" dir="rtl" value="{{ $featuredProduct['link_aria_label']['ar'] ?? '' }}">
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Featured Media Banner ───────────────────────────────────────── --}}
  <div class="accordion-item border-0 shadow-sm mb-3 rounded">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#sec-fmb">
        <i class="fa-solid fa-video me-2 text-danger"></i> Featured Media Banner
      </button>
    </h2>
    <div id="sec-fmb" class="accordion-collapse collapse">
      <div class="accordion-body">
        <div class="row g-3">
          <div class="col-12">
            <label class="form-label fw-semibold">Video MP4 URL</label>
            <input name="fmb_video_mp4" class="form-control" value="{{ $featuredMediaBanner['video_mp4'] ?? '' }}">
          </div>
          <div class="col-12">
            <label class="form-label fw-semibold">Video Poster URL <span class="text-muted fw-normal">(optional)</span></label>
            <input name="fmb_video_poster" class="form-control" value="{{ $featuredMediaBanner['video_poster'] ?? '' }}">
          </div>
          <div class="col-md-6"><label class="form-label fw-semibold">Eyebrow (EN)</label><input name="fmb_eyebrow_en" class="form-control" value="{{ $featuredMediaBanner['eyebrow']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Eyebrow (AR)</label><input name="fmb_eyebrow_ar" class="form-control" dir="rtl" value="{{ $featuredMediaBanner['eyebrow']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Title (EN)</label><input name="fmb_title_en" class="form-control" value="{{ $featuredMediaBanner['title']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Title (AR)</label><input name="fmb_title_ar" class="form-control" dir="rtl" value="{{ $featuredMediaBanner['title']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Description (EN)</label><textarea name="fmb_desc_en" class="form-control" rows="2">{{ $featuredMediaBanner['description']['en'] ?? '' }}</textarea></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Description (AR)</label><textarea name="fmb_desc_ar" class="form-control" dir="rtl" rows="2">{{ $featuredMediaBanner['description']['ar'] ?? '' }}</textarea></div>
          <div class="col-md-4"><label class="form-label fw-semibold">CTA Label (EN)</label><input name="fmb_cta_label_en" class="form-control" value="{{ $featuredMediaBanner['cta_label']['en'] ?? '' }}"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">CTA Label (AR)</label><input name="fmb_cta_label_ar" class="form-control" dir="rtl" value="{{ $featuredMediaBanner['cta_label']['ar'] ?? '' }}"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">CTA Path</label><input name="fmb_cta_path" class="form-control" value="{{ $featuredMediaBanner['cta_path'] ?? '/shop' }}"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Apple Watch Promo ───────────────────────────────────────────── --}}
  <div class="accordion-item border-0 shadow-sm mb-3 rounded">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#sec-aw">
        <i class="fa-brands fa-apple me-2"></i> Apple Watch Promo Banner
      </button>
    </h2>
    <div id="sec-aw" class="accordion-collapse collapse">
      <div class="accordion-body">
        <div class="row g-3">
          <div class="col-12"><label class="form-label fw-semibold">Banner (Mobile) URL</label><input name="aw_banner_mobile" class="form-control" value="{{ $appleWatchPromo['banner_src_mobile'] ?? '' }}"></div>
          <div class="col-12"><label class="form-label fw-semibold">Banner (Desktop) URL</label><input name="aw_banner_desktop" class="form-control" value="{{ $appleWatchPromo['banner_src_desktop'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Eyebrow (EN)</label><input name="aw_eyebrow_en" class="form-control" value="{{ $appleWatchPromo['eyebrow']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Eyebrow (AR)</label><input name="aw_eyebrow_ar" class="form-control" dir="rtl" value="{{ $appleWatchPromo['eyebrow']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Title (EN)</label><input name="aw_title_en" class="form-control" value="{{ $appleWatchPromo['title']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Title (AR)</label><input name="aw_title_ar" class="form-control" dir="rtl" value="{{ $appleWatchPromo['title']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Subtitle (EN)</label><input name="aw_subtitle_en" class="form-control" value="{{ $appleWatchPromo['subtitle']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Subtitle (AR)</label><input name="aw_subtitle_ar" class="form-control" dir="rtl" value="{{ $appleWatchPromo['subtitle']['ar'] ?? '' }}"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">CTA Label (EN)</label><input name="aw_cta_label_en" class="form-control" value="{{ $appleWatchPromo['cta_label']['en'] ?? '' }}"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">CTA Label (AR)</label><input name="aw_cta_label_ar" class="form-control" dir="rtl" value="{{ $appleWatchPromo['cta_label']['ar'] ?? '' }}"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">CTA Path</label><input name="aw_cta_path" class="form-control" value="{{ $appleWatchPromo['cta_path'] ?? '/shop' }}"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Newsletter ───────────────────────────────────────────────────── --}}
  <div class="accordion-item border-0 shadow-sm mb-3 rounded">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#sec-nl">
        <i class="fa-solid fa-envelope me-2 text-info"></i> Newsletter (Trust Section)
      </button>
    </h2>
    <div id="sec-nl" class="accordion-collapse collapse">
      <div class="accordion-body">
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label fw-semibold">Title (EN)</label><input name="trust_nl_title_en" class="form-control" value="{{ $trust['newsletter']['title']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Title (AR)</label><input name="trust_nl_title_ar" class="form-control" dir="rtl" value="{{ $trust['newsletter']['title']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Email Placeholder (EN)</label><input name="trust_nl_email_en" class="form-control" value="{{ $trust['newsletter']['email_placeholder']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Email Placeholder (AR)</label><input name="trust_nl_email_ar" class="form-control" dir="rtl" value="{{ $trust['newsletter']['email_placeholder']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Submit Button (EN)</label><input name="trust_nl_submit_en" class="form-control" value="{{ $trust['newsletter']['submit_label']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Submit Button (AR)</label><input name="trust_nl_submit_ar" class="form-control" dir="rtl" value="{{ $trust['newsletter']['submit_label']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Disclaimer Intro (EN)</label><input name="trust_nl_disclaimer_en" class="form-control" value="{{ $trust['newsletter']['disclaimer_intro']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Disclaimer Intro (AR)</label><input name="trust_nl_disclaimer_ar" class="form-control" dir="rtl" value="{{ $trust['newsletter']['disclaimer_intro']['ar'] ?? '' }}"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Privacy Link Label (EN)</label><input name="trust_nl_privacy_en" class="form-control" value="{{ $trust['newsletter']['privacy_link_label']['en'] ?? '' }}"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Privacy Link Label (AR)</label><input name="trust_nl_privacy_ar" class="form-control" dir="rtl" value="{{ $trust['newsletter']['privacy_link_label']['ar'] ?? '' }}"></div>
          <div class="col-md-4"><label class="form-label fw-semibold">Privacy Path</label><input name="trust_nl_privacy_path" class="form-control" value="{{ $trust['newsletter']['privacy_path'] ?? '/contact' }}"></div>
        </div>
      </div>
    </div>
  </div>

  {{-- ── Reviews + FAQ Labels ────────────────────────────────────────── --}}
  <div class="accordion-item border-0 shadow-sm mb-3 rounded">
    <h2 class="accordion-header">
      <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse" data-bs-target="#sec-misc">
        <i class="fa-solid fa-sliders me-2 text-secondary"></i> Reviews & FAQ Headings
      </button>
    </h2>
    <div id="sec-misc" class="accordion-collapse collapse">
      <div class="accordion-body">
        <p class="text-muted small mb-3">Section headings for the Customer Reviews and FAQ sections.</p>
        <div class="row g-3">
          <div class="col-md-6"><label class="form-label fw-semibold">Reviews Title (EN)</label><input name="rev_title_en" class="form-control" value="{{ $reviewsMeta['title']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Reviews Title (AR)</label><input name="rev_title_ar" class="form-control" dir="rtl" value="{{ $reviewsMeta['title']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Reviews Subtitle (EN)</label><input name="rev_sub_en" class="form-control" value="{{ $reviewsMeta['subtitle']['en'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">Reviews Subtitle (AR)</label><input name="rev_sub_ar" class="form-control" dir="rtl" value="{{ $reviewsMeta['subtitle']['ar'] ?? '' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">FAQ Title (EN)</label><input name="faq_title_en" class="form-control" value="{{ $faqMeta['title']['en'] ?? 'FAQ' }}"></div>
          <div class="col-md-6"><label class="form-label fw-semibold">FAQ Title (AR)</label><input name="faq_title_ar" class="form-control" dir="rtl" value="{{ $faqMeta['title']['ar'] ?? 'الأسئلة الشائعة' }}"></div>
        </div>
      </div>
    </div>
  </div>

</div>{{-- /accordion --}}

<div class="mt-4">
  <button type="submit" class="btn btn-primary px-4">
    <i class="fa-solid fa-floppy-disk me-2"></i>Save Settings
  </button>
  <span class="text-muted small ms-3">Changes reflect on the storefront immediately after saving.</span>
</div>

</form>
@endsection
