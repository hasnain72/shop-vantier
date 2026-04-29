@csrf
<div class="row g-3">
  <div class="col-lg-8">
    <div class="card border-0 shadow-sm mb-3">
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Title <span class="text-danger">*</span></label>
          <input name="title" class="form-control @error('title') is-invalid @enderror"
            value="{{ old('title', $article->title ?? '') }}" required>
          @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
          <label class="form-label">Excerpt / Summary</label>
          <textarea name="summary_html" class="form-control" rows="3"
            >{{ old('summary_html', $article->summary_html ?? '') }}</textarea>
        </div>
        <div>
          <label class="form-label">Content</label>
          <textarea name="body_html" class="form-control" rows="14"
            >{{ old('body_html', $article->body_html ?? '') }}</textarea>
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
  </div>
</div>
