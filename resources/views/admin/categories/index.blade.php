@extends('layouts.admin')

@section('title', 'Category Management - ChessLiv Admin')

@section('admin_content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h2 class="font-heading fs-3 text-white m-0">
            <i class="bi bi-folder2-open text-warning me-2"></i>Editorial Category Management
        </h2>
        <p class="text-secondary small m-0">
            Organize news sections, configure taxonomy slugs, adjust display ordering, and control public visibility.
        </p>
    </div>
    <div>
        <button type="button" class="btn btn-warning btn-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
            <i class="bi bi-plus-lg me-1"></i>Create New Category
        </button>
    </div>
</div>

{{-- Filters Card --}}
<div class="card admin-card mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.categories.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-dark text-secondary border-secondary"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control bg-dark text-white border-secondary" placeholder="Search categories by name, slug or description..." value="{{ request('q') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm bg-dark text-white border-secondary">
                    <option value="">All Categories</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
            </div>
            <div class="col-md-2 text-md-end">
                <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Categories Table --}}
<div class="card admin-card mb-4 shadow">
    <div class="card-header bg-black bg-opacity-40 border-secondary d-flex justify-content-between align-items-center py-3">
        <h5 class="fs-6 fw-bold text-white m-0">
            <i class="bi bi-tags-fill me-2 text-warning"></i>Active Taxonomies ({{ $categories->total() }} Total)
        </h5>
        @if(request()->anyFilled(['q', 'status']))
            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-outline-secondary py-0">Reset Filters</a>
        @endif
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle mb-0 small">
            <thead class="table-dark text-secondary text-uppercase" style="font-size: 0.75rem;">
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Category Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th class="text-center">Order</th>
                    <th class="text-center">Articles</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td class="text-muted">{{ $cat->id }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-secondary p-2"><i class="bi {{ $cat->icon }} text-warning"></i></span>
                                <div>
                                    <strong class="text-white">{{ $cat->name }}</strong>
                                </div>
                            </div>
                        </td>
                        <td class="font-monospace text-secondary">
                            <code>{{ $cat->slug }}</code>
                        </td>
                        <td class="text-secondary" style="max-width: 250px;">
                            {{ Str::limit($cat->description ?: 'No description provided.', 80) }}
                        </td>
                        <td class="text-center font-monospace">{{ $cat->order }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill">
                                {{ $cat->articles_count }}
                            </span>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.categories.toggle', $cat->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm py-0 px-2 {{ $cat->is_active ? 'btn-outline-success' : 'btn-outline-secondary' }}" title="Click to toggle active status">
                                    {{ $cat->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </form>
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-warning py-0 px-2 btn-edit-category"
                                        data-id="{{ $cat->id }}"
                                        data-name="{{ $cat->name }}"
                                        data-slug="{{ $cat->slug }}"
                                        data-icon="{{ $cat->icon }}"
                                        data-order="{{ $cat->order }}"
                                        data-description="{{ $cat->description }}"
                                        data-active="{{ $cat->is_active ? 1 : 0 }}"
                                        title="Edit Category">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-outline-danger py-0 px-2 btn-delete-category"
                                        data-id="{{ $cat->id }}"
                                        data-name="{{ $cat->name }}"
                                        data-count="{{ $cat->articles_count }}"
                                        title="Delete Category">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-secondary">
                            <i class="bi bi-folder-x fs-3 d-block mb-2"></i>
                            No categories found matching your query.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
        <div class="card-footer bg-black bg-opacity-25 border-secondary py-3">
            {{ $categories->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

{{-- Create Category Modal --}}
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary shadow-lg">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title font-heading fs-6 text-white" id="createCategoryModalLabel">
                        <i class="bi bi-folder-plus text-warning me-2"></i>Create New Category
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-secondary">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm bg-black text-white border-secondary" required placeholder="e.g. Rapid &amp; Blitz">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary">Slug (optional)</label>
                        <input type="text" name="slug" class="form-control form-control-sm bg-black text-white border-secondary" placeholder="e.g. rapid-blitz (auto-generated if empty)">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label small text-secondary">Bootstrap Icon Class</label>
                            <input type="text" name="icon" class="form-control form-control-sm bg-black text-white border-secondary" value="bi-newspaper" placeholder="e.g. bi-trophy, bi-lightning">
                        </div>
                        <div class="col-4">
                            <label class="form-label small text-secondary">Display Order</label>
                            <input type="number" name="order" class="form-control form-control-sm bg-black text-white border-secondary" value="10" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary">Editorial Description</label>
                        <textarea name="description" class="form-control form-control-sm bg-black text-white border-secondary" rows="3" placeholder="Brief summary for archive and SEO header..."></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="catActiveSwitch" checked>
                        <label class="form-check-label small text-secondary" for="catActiveSwitch">Active &amp; Visible in Main Navigation</label>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold">Create Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Category Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary shadow-lg">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-secondary">
                    <h5 class="modal-title font-heading fs-6 text-white" id="editCategoryModalLabel">
                        <i class="bi bi-pencil-square text-warning me-2"></i>Edit Category: <span id="editCatTitle"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small text-secondary">Category Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editCatName" class="form-control form-control-sm bg-black text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary">Slug</label>
                        <input type="text" name="slug" id="editCatSlug" class="form-control form-control-sm bg-black text-white border-secondary">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-8">
                            <label class="form-label small text-secondary">Bootstrap Icon Class</label>
                            <input type="text" name="icon" id="editCatIcon" class="form-control form-control-sm bg-black text-white border-secondary">
                        </div>
                        <div class="col-4">
                            <label class="form-label small text-secondary">Display Order</label>
                            <input type="number" name="order" id="editCatOrder" class="form-control form-control-sm bg-black text-white border-secondary" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-secondary">Editorial Description</label>
                        <textarea name="description" id="editCatDescription" class="form-control form-control-sm bg-black text-white border-secondary" rows="3"></textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="editCatActive">
                        <label class="form-check-label small text-secondary" for="editCatActive">Active &amp; Visible in Main Navigation</label>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Category Modal --}}
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-secondary shadow-lg">
            <form id="deleteCategoryForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header border-secondary">
                    <h5 class="modal-title font-heading fs-6 text-danger" id="deleteCategoryModalLabel">
                        <i class="bi bi-trash-fill me-2"></i>Delete Category Confirmation
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="deleteCatName" class="text-warning"></strong>?</p>
                    <div class="alert alert-secondary small mb-0">
                        <i class="bi bi-shield-check text-success me-1"></i>
                        This category contains <strong id="deleteCatArticlesCount"></strong> articles. They will automatically be safely reassigned to an active default category inside a database transaction to prevent any data loss or orphaned articles.
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger btn-sm fw-bold">Delete Category Safely</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function() {
    // Edit Modal handler
    $('.btn-edit-category').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var slug = $(this).data('slug');
        var icon = $(this).data('icon');
        var order = $(this).data('order');
        var description = $(this).data('description');
        var active = $(this).data('active');

        $('#editCatTitle').text(name);
        $('#editCatName').val(name);
        $('#editCatSlug').val(slug);
        $('#editCatIcon').val(icon);
        $('#editCatOrder').val(order);
        $('#editCatDescription').val(description);
        $('#editCatActive').prop('checked', active == 1);

        $('#editCategoryForm').attr('action', '/admin/categories/' + id);
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    });

    // Delete Modal handler
    $('.btn-delete-category').on('click', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var count = $(this).data('count');

        $('#deleteCatName').text(name);
        $('#deleteCatArticlesCount').text(count);
        $('#deleteCategoryForm').attr('action', '/admin/categories/' + id);
        new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
    });
});
</script>
@endpush
