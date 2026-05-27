@csrf
<div class="row g-3">
  <div class="col-lg-8">

    {{-- Language tabs --}}
    <ul class="nav nav-tabs mb-0" id="articleLangTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-en" data-bs-toggle="tab" data-bs-target="#content-en"
          type="button" role="tab">🇬🇧 English</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-ar" data-bs-toggle="tab" data-bs-target="#content-ar"
          type="button" role="tab">🇸🇦 Arabic</button>
      </li>
    </ul>

    <div class="tab-content border border-top-0 rounded-bottom p-3 bg-white shadow-sm mb-3">

      {{-- English content --}}
      <div class="tab-pane fade show active" id="content-en" role="tabpanel">
        <div class="mb-3">
          <label class="form-label">Title (English) <span class="text-danger">*</span></label>
          <input name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $article->title ?? '') }}" required>
          @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">Excerpt / Summary (English)</label>
          <textarea name="summary_html" class="form-control" rows="3"
            >{{ old('summary_html', $article->summary_html ?? '') }}</textarea>
        </div>
        <div>
          <label class="form-label">Content (English)</label>
          <textarea name="body_html" class="form-control" rows="14"
            >{{ old('body_html', $article->body_html ?? '') }}</textarea>
          <div class="form-text">HTML is supported.</div>
        </div>
      </div>

      {{-- Arabic content --}}
      <div class="tab-pane fade" id="content-ar" role="tabpanel" dir="rtl">
        <div class="mb-3">
          <label class="form-label">العنوان (عربي)</label>
          <input name="title_ar" class="form-control" dir="rtl"
            value="{{ old('title_ar', $article->title_ar ?? '') }}"
            placeholder="أدخل العنوان بالعربية">
        </div>
        <div class="mb-3">
          <label class="form-label">اسم الكاتب (عربي)</label>
          <input name="author_ar" class="form-control" dir="rtl"
            value="{{ old('author_ar', $article->author_ar ?? '') }}"
            placeholder="مثال: حسن شهزاد">
        </div>
        <div class="mb-3">
          <label class="form-label">المقتطف / الملخص (عربي)</label>
          <textarea name="summary_html_ar" class="form-control" rows="3" dir="rtl"
            placeholder="مقتطف قصير بالعربية…"
            >{{ old('summary_html_ar', $article->summary_html_ar ?? '') }}</textarea>
        </div>
        <div>
          <label class="form-label">المحتوى (عربي)</label>
          <textarea name="body_html_ar" class="form-control" rows="14" dir="rtl"
            placeholder="محتوى المقال بالعربية — يدعم HTML"
            >{{ old('body_html_ar', $article->body_html_ar ?? '') }}</textarea>
          <div class="form-text">يدعم HTML.</div>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">SEO</div>
        <div class="mb-3">
          <label class="form-label small">Meta title</label>
          <input name="meta_title" class="form-control"
            value="{{ old('meta_title', $article->meta_title ?? '') }}">
        </div>
        <div>
          <label class="form-label small">Meta description</label>
          <textarea name="meta_description" class="form-control" rows="2"
            >{{ old('meta_description', $article->meta_description ?? '') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">Publishing</div>
        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="published" id="published" value="1"
            {{ old('published', $article->published ?? false) ? 'checked' : '' }}>
          <label class="form-check-label" for="published">Published</label>
        </div>
        <label class="form-label small">Tags</label>
        <input name="tags" class="form-control"
          value="{{ old('tags', isset($article) ? implode(', ', $article->tags ?? []) : '') }}"
          placeholder="watch, tips, leather">
        <div class="form-text">Comma-separated</div>
      </div>
    </div>

    {{-- Arabic completeness indicator --}}
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-2 small">Arabic Translation</div>
        @if(isset($article))
          @php
            $arFields = collect([$article->title_ar, $article->body_html_ar, $article->summary_html_ar]);
            $filled   = $arFields->filter()->count();
          @endphp
          <div class="progress mb-2" style="height:6px;">
            <div class="progress-bar bg-success" style="width:{{ ($filled / 3) * 100 }}%"></div>
          </div>
          <p class="small text-secondary mb-0">{{ $filled }}/3 fields translated</p>
        @else
          <p class="small text-secondary mb-0">Fill in the Arabic tab to enable bilingual content.</p>
        @endif
      </div>
    </div>
  </div>
</div>
