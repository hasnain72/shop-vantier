{{-- Generic confirm modal triggered by data-confirm-form="#formId" --}}
<div class="modal fade" id="confirmModal" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <i class="fa-solid fa-triangle-exclamation fa-2x text-warning mb-3 d-block"></i>
        <div class="fw-semibold mb-1" id="confirmTitle">Are you sure?</div>
        <div class="text-secondary small" id="confirmMessage">This action cannot be undone.</div>
      </div>
      <div class="modal-footer justify-content-center gap-2">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
(function () {
  var modal   = document.getElementById('confirmModal');
  var confirmBtn = document.getElementById('confirmBtn');
  var targetForm = null;

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-confirm-form]');
    if (!btn) return;
    e.preventDefault();
    targetForm = document.querySelector(btn.dataset.confirmForm);
    var title  = btn.dataset.confirmTitle   || 'Are you sure?';
    var msg    = btn.dataset.confirmMessage || 'This action cannot be undone.';
    var label  = btn.dataset.confirmLabel   || 'Delete';
    document.getElementById('confirmTitle').textContent   = title;
    document.getElementById('confirmMessage').textContent = msg;
    document.getElementById('confirmBtn').textContent     = label;
    new bootstrap.Modal(modal).show();
  });

  confirmBtn.addEventListener('click', function () {
    if (targetForm) targetForm.submit();
  });
})();
</script>
@endpush
