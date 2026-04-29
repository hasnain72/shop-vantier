@csrf
<div class="row g-3">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Title <span class="text-danger">*</span></label>
          <input name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $page->title ?? '') }}" required>
          @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">Content</label>
          <textarea name="body_html" id="bodyEditor" class="form-control" rows="14"
            >{{ old('body_html', $page->body_html ?? '') }}</textarea>
        </div>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="fw-semibold mb-3">SEO</div>
        <div class="mb-3">
          <label class="form-label small">Meta title</label>
          <input name="meta_title" class="form-control"
            value="{{ old('meta_title', $page->meta_title ?? '') }}">
        </div>
        <div>
          <label class="form-label small">Meta description</label>
          <textarea name="meta_description" class="form-control" rows="2"
            >{{ old('meta_description', $page->meta_description ?? '') }}</textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-semibold mb-3">Visibility</div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="published" id="published" value="1"
            {{ old('published', $page->published ?? true) ? 'checked' : '' }}>
          <label class="form-check-label" for="published">Visible (published)</label>
        </div>
      </div>
    </div>
  </div>
</div>
