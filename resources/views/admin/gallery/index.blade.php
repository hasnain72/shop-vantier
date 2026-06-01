@extends('admin.layouts.app')
@section('title', 'Media Gallery')

@section('content')
<div class="container-fluid py-4">

  {{-- Header --}}
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h4 class="mb-0 fw-semibold">Media Gallery</h4>
      <p class="text-muted small mb-0">Upload images and files — copy URLs to use anywhere.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
      <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Upload Files
    </button>
  </div>

  {{-- Alerts --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  {{-- Filter bar --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-2">
      <form method="GET" action="{{ route('admin.gallery.index') }}" class="row g-2 align-items-center">
        <div class="col-auto">
          <select name="folder" class="form-select form-select-sm" onchange="this.form.submit()">
            <option value="all" {{ request('folder','all') === 'all' ? 'selected' : '' }}>All folders</option>
            @foreach($folders as $f)
              <option value="{{ $f }}" {{ request('folder') === $f ? 'selected' : '' }}>{{ ucfirst($f) }}</option>
            @endforeach
          </select>
        </div>
        <div class="col">
          <div class="input-group input-group-sm">
            <input type="text" name="q" class="form-control" placeholder="Search by name…" value="{{ request('q') }}">
            <button class="btn btn-outline-secondary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
          </div>
        </div>
        @if(request('q') || (request('folder') && request('folder') !== 'all'))
          <div class="col-auto">
            <a href="{{ route('admin.gallery.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
          </div>
        @endif
        <div class="col-auto ms-auto text-muted small">
          {{ $files->total() }} file{{ $files->total() !== 1 ? 's' : '' }}
        </div>
      </form>
    </div>
  </div>

  {{-- Grid --}}
  @if($files->isEmpty())
    <div class="text-center py-5 text-muted">
      <i class="fa-regular fa-image fa-3x mb-3 opacity-25"></i>
      <p class="mb-0">No files yet. Upload your first file above.</p>
    </div>
  @else
    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-xl-6 g-3" id="mediaGrid">
      @foreach($files as $file)
        <div class="col" data-id="{{ $file->id }}">
          <div class="card border-0 shadow-sm h-100 media-card" style="border-radius:10px;overflow:hidden;">

            {{-- Thumbnail --}}
            <div class="media-thumb bg-light d-flex align-items-center justify-content-center"
                 style="height:130px;cursor:pointer;overflow:hidden;"
                 onclick="openPreview('{{ addslashes($file->url) }}','{{ addslashes($file->name) }}','{{ $file->is_image ? 'image' : 'other' }}')">
              @if($file->is_image)
                <img src="{{ $file->url }}" alt="{{ $file->alt ?? $file->name }}"
                     style="width:100%;height:100%;object-fit:cover;" loading="lazy">
              @elseif(str_starts_with($file->mime_type,'video/'))
                <i class="fa-solid fa-circle-play fa-3x text-secondary opacity-50"></i>
              @elseif($file->mime_type === 'application/pdf')
                <i class="fa-solid fa-file-pdf fa-3x text-danger opacity-60"></i>
              @else
                <i class="fa-solid fa-file fa-3x text-secondary opacity-50"></i>
              @endif
            </div>

            {{-- Info --}}
            <div class="card-body p-2">
              <p class="mb-0 text-truncate small fw-medium" title="{{ $file->name }}" style="font-size:.75rem;">
                {{ $file->name }}
              </p>
              <p class="mb-1 text-muted" style="font-size:.68rem;">
                {{ strtoupper(pathinfo($file->file_name, PATHINFO_EXTENSION)) }} · {{ $file->human_size }}
              </p>

              {{-- Actions --}}
              <div class="d-flex gap-1">
                {{-- Copy URL --}}
                <button type="button"
                        class="btn btn-sm btn-outline-primary flex-fill py-0 copy-btn"
                        style="font-size:.7rem;"
                        data-url="{{ $file->url }}"
                        title="Copy URL">
                  <i class="fa-solid fa-copy"></i> Copy URL
                </button>

                {{-- Edit --}}
                <button type="button"
                        class="btn btn-sm btn-outline-secondary py-0 px-2"
                        title="Edit"
                        onclick="openEdit({{ $file->id }},'{{ addslashes($file->name) }}','{{ addslashes($file->alt ?? '') }}')">
                  <i class="fa-solid fa-pen"></i>
                </button>

                {{-- Delete --}}
                <form method="POST"
                      action="{{ route('admin.gallery.destroy', $file->id) }}"
                      class="d-inline"
                      onsubmit="return confirm('Delete this file permanently?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </div>
            </div>

          </div>
        </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $files->links() }}
    </div>
  @endif
</div>

{{-- ══ Upload Modal ═══════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="uploadModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title">Upload Files</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">

        {{-- Folder --}}
        <div class="mb-3 row align-items-center g-2">
          <label class="col-auto col-form-label small fw-semibold">Folder</label>
          <div class="col">
            <div class="input-group input-group-sm">
              <select id="folderSelect" class="form-select">
                <option value="general">general</option>
                @foreach($folders as $f)
                  <option value="{{ $f }}">{{ $f }}</option>
                @endforeach
                <option value="__new__">+ New folder…</option>
              </select>
              <input id="folderCustom" type="text" class="form-control d-none" placeholder="folder-name">
            </div>
          </div>
        </div>

        {{-- Dropzone --}}
        <div id="mediaDropzone" class="dropzone rounded border-2 border-dashed"
             style="border-color:#dee2e6;min-height:180px;background:#f8f9fa;">
          <div class="dz-message text-center py-4">
            <i class="fa-solid fa-cloud-arrow-up fa-2x text-muted mb-2"></i>
            <p class="mb-0 text-muted">Drag & drop files here or <span class="text-primary fw-semibold">click to browse</span></p>
            <small class="text-muted">Images, PDF, Video · Max {{ 20 }} MB each</small>
          </div>
        </div>

        <div id="uploadStatus" class="mt-2"></div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="uploadBtn" class="btn btn-primary">
          <i class="fa-solid fa-arrow-up-from-bracket me-1"></i> Upload
        </button>
      </div>
    </div>
  </div>
</div>

{{-- ══ Edit Modal ══════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title small">Edit file info</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="editFileId">
        <div class="mb-2">
          <label class="form-label small fw-semibold mb-1">Name</label>
          <input type="text" id="editName" class="form-control form-control-sm">
        </div>
        <div class="mb-2">
          <label class="form-label small fw-semibold mb-1">Alt text</label>
          <input type="text" id="editAlt" class="form-control form-control-sm" placeholder="Describe the image…">
        </div>
      </div>
      <div class="modal-footer border-0 pt-0">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="editSaveBtn" class="btn btn-primary btn-sm">Save</button>
      </div>
    </div>
  </div>
</div>

{{-- ══ Preview Modal ════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="previewModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow bg-dark">
      <div class="modal-header border-0 pb-0">
        <span id="previewTitle" class="text-white small"></span>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-2">
        <img id="previewImg" src="" alt="" style="max-width:100%;max-height:75vh;border-radius:6px;" class="d-none">
        <div id="previewOther" class="text-white py-5 d-none">
          <i class="fa-solid fa-file fa-4x opacity-50 mb-3"></i>
          <p>Preview not available.</p>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
Dropzone.autoDiscover = false;

// ── folder select ───────────────────────────────────────────────────────────
const folderSelect = document.getElementById('folderSelect');
const folderCustom = document.getElementById('folderCustom');
folderSelect.addEventListener('change', () => {
  folderCustom.classList.toggle('d-none', folderSelect.value !== '__new__');
});

function getFolder() {
  return folderSelect.value === '__new__'
    ? (folderCustom.value.trim() || 'general')
    : folderSelect.value;
}

// ── Dropzone ────────────────────────────────────────────────────────────────
const dz = new Dropzone('#mediaDropzone', {
  url: '{{ route('admin.gallery.store') }}',
  autoProcessQueue: false,
  uploadMultiple: true,
  parallelUploads: 10,
  maxFilesize: 20,
  paramName: 'files',
  headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
  addRemoveLinks: true,
  dictRemoveFile: '×',
  init: function () {
    const dzInstance = this;

    document.getElementById('uploadBtn').addEventListener('click', () => {
      if (!dzInstance.getQueuedFiles().length && !dzInstance.getUploadingFiles().length) {
        showUploadStatus('warning', 'Please add at least one file.');
        return;
      }
      dzInstance.options.params = { folder: getFolder() };
      dzInstance.processQueue();
    });

    dzInstance.on('sendingmultiple', (files, xhr, formData) => {
      formData.append('folder', getFolder());
    });

    dzInstance.on('successmultiple', (files, response) => {
      showUploadStatus('success', `${files.length} file(s) uploaded successfully.`);
      setTimeout(() => location.reload(), 1200);
    });

    dzInstance.on('errormultiple', (files, msg) => {
      const text = typeof msg === 'string' ? msg : (msg?.message ?? 'Upload failed.');
      showUploadStatus('danger', text);
    });
  },
});

function showUploadStatus(type, msg) {
  document.getElementById('uploadStatus').innerHTML =
    `<div class="alert alert-${type} py-2 small mb-0">${msg}</div>`;
}

// reset dropzone when modal closes
document.getElementById('uploadModal').addEventListener('hidden.bs.modal', () => {
  dz.removeAllFiles(true);
  document.getElementById('uploadStatus').innerHTML = '';
  folderCustom.classList.add('d-none');
  folderSelect.value = 'general';
});

// ── Copy URL ─────────────────────────────────────────────────────────────────
document.querySelectorAll('.copy-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    navigator.clipboard.writeText(btn.dataset.url).then(() => {
      const orig = btn.innerHTML;
      btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
      btn.classList.replace('btn-outline-primary', 'btn-success');
      setTimeout(() => {
        btn.innerHTML = orig;
        btn.classList.replace('btn-success', 'btn-outline-primary');
      }, 1800);
    });
  });
});

// ── Edit ──────────────────────────────────────────────────────────────────────
function openEdit(id, name, alt) {
  document.getElementById('editFileId').value = id;
  document.getElementById('editName').value   = name;
  document.getElementById('editAlt').value    = alt;
  new bootstrap.Modal(document.getElementById('editModal')).show();
}

document.getElementById('editSaveBtn').addEventListener('click', () => {
  const id   = document.getElementById('editFileId').value;
  const name = document.getElementById('editName').value.trim();
  const alt  = document.getElementById('editAlt').value.trim();
  if (!name) return;

  fetch(`{{ url('admin/gallery') }}/${id}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
      'Accept': 'application/json',
    },
    body: JSON.stringify({ name, alt }),
  })
  .then(r => r.json())
  .then(res => {
    if (res.success) {
      bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
      location.reload();
    }
  });
});

// ── Preview ───────────────────────────────────────────────────────────────────
function openPreview(url, name, type) {
  document.getElementById('previewTitle').textContent = name;
  const img   = document.getElementById('previewImg');
  const other = document.getElementById('previewOther');
  if (type === 'image') {
    img.src = url; img.classList.remove('d-none'); other.classList.add('d-none');
  } else {
    img.classList.add('d-none'); other.classList.remove('d-none');
  }
  new bootstrap.Modal(document.getElementById('previewModal')).show();
}
</script>
@endpush
@endsection
