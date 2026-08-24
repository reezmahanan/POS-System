@extends('layouts.admin')

@section('title', 'POS - Categories')
@section('page_title', 'Category Management')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold text-secondary">Categories List</h5>
                @if(Auth::user()->role === 'admin')
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-plus-circle me-1"></i> Add Category
                    </button>
                @endif
            </div>
            
            <div class="card-body">
                <!-- Search bar -->
                <form action="{{ route('categories.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ $search }}">
                            <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
                            @if($search)
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-danger"><i class="bi bi-x-circle"></i> Clear</a>
                            @endif
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th>Category Name</th>
                                <th>Total Products</th>
                                <th>Created At</th>
                                @if(Auth::user()->role === 'admin')
                                    <th style="width: 200px;" class="text-end pe-3">Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $category->id }}</td>
                                    <td class="fw-semibold">{{ $category->name }}</td>
                                    <td>
                                        <span class="badge bg-info text-white">{{ $category->products_count }} Products</span>
                                    </td>
                                    <td>{{ $category->created_at->format('M d, Y') }}</td>
                                    @if(Auth::user()->role === 'admin')
                                        <td class="text-end pe-3">
                                            <!-- Edit Button (triggers modal) -->
                                            <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $category->id }}">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>

                                            <!-- Delete Form -->
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category? All products in it will also be deleted!');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>

                                @if(Auth::user()->role === 'admin')
                                    <!-- Edit Category Modal for this specific category -->
                                    <div class="modal fade" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-labelledby="editCategoryModalLabel{{ $category->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold" id="editCategoryModalLabel{{ $category->id }}">Edit Category</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="name{{ $category->id }}" class="form-label text-secondary">Category Name</label>
                                                            <input type="text" class="form-control" id="name{{ $category->id }}" name="name" value="{{ $category->name }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@if(Auth::user()->role === 'admin')
    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addCategoryModalLabel">Add New Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="add_name" class="form-label text-secondary">Category Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="add_name" name="name" value="{{ old('name') }}" required placeholder="e.g. Beverages">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
