@extends('dashboard.layouts.app')

@push('css')
<style>
    .perm-card {
        border: 1px solid #e4e6ef;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        height: 100%;
    }
    .perm-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f5f8fa;
        padding: 10px 14px;
        border-bottom: 1px solid #e4e6ef;
    }
    .perm-group-title {
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #3f4254;
    }
    .perm-card-actions {
        display: flex;
        gap: 6px;
    }
    .perm-action-btn {
        cursor: pointer;
        color: #a1a5b7;
        font-size: 13px;
        padding: 2px 6px;
        border-radius: 4px;
        transition: color 0.15s, background 0.15s;
    }
    .perm-action-btn:hover {
        color: #009ef7;
        background: #e8f4fd;
    }
    .perm-action-btn.reset:hover {
        color: #f1416c;
        background: #fff0f3;
    }
    .perm-card-body {
        padding: 12px 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .perm-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #5e6278;
        cursor: pointer;
        margin: 0;
        padding: 3px 0;
        transition: color 0.15s;
    }
    .perm-item:hover {
        color: #009ef7;
    }
    .perm-item input[type="checkbox"] {
        accent-color: #009ef7;
        width: 15px;
        height: 15px;
        cursor: pointer;
        flex-shrink: 0;
    }
    .global-action-btn {
        font-size: 12px;
        color: #009ef7;
        cursor: pointer;
        padding: 4px 10px;
        border: 1px solid #009ef7;
        border-radius: 5px;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        text-decoration: none;
        background: transparent;
    }
    .global-action-btn:hover {
        background: #009ef7;
        color: #fff;
    }
    .global-action-btn.danger {
        color: #f1416c;
        border-color: #f1416c;
    }
    .global-action-btn.danger:hover {
        background: #f1416c;
        color: #fff;
    }
</style>
@endpush

@php
    $isEdit  = isset($role);
    $title   = $isEdit ? 'Edit Role' : 'Create Role';
    $action  = $isEdit ? route('role.update', $role->id) : route('role.store');
    $btnText = $isEdit ? 'Update Role' : 'Create Role';

    $allPermissions = \Spatie\Permission\Models\Permission::all()->groupBy('group');

    $rolePermissions = $isEdit ? $role->permissions->pluck('name')->toArray() : [];
@endphp

@section('content')
<div class="app-container container-xxl">
    <div class="card shadow-sm">

        <div class="card-header border-1 pt-4 pb-2">
            <div class="card-title">
                <h4 class="mb-0">
                    <i class="fa fa-shield text-primary me-2"></i>{{ $title }}
                </h4>
            </div>
            <div class="card-toolbar">
                <a href="{{ route('role.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fa fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <div class="card-body">
            <form action="{{ $action }}" method="POST" novalidate>
                @csrf
                @if($isEdit) @method('PUT') @endif

                {{-- Role Name --}}
                <div class="mb-5">
                    <label for="name" class="form-label fw-semibold">Role Name</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $role->name ?? '') }}"
                        placeholder="e.g. manager" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Permissions --}}
                @if($allPermissions->count())
                <div class="mb-5">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <label class="form-label fw-bold mb-0">Permissions</label>
                        <div class="d-flex gap-2">
                            <span class="global-action-btn" id="selectAllBtn">
                                <i class="fa fa-check"></i> Select All
                            </span>
                            <span class="global-action-btn danger" id="deselectAllBtn">
                                <i class="fa fa-refresh"></i> Deselect All
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        @foreach($allPermissions as $group => $groupPerms)
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                            <div class="perm-card">
                                <div class="perm-card-header">
                                    <span class="perm-group-title">
                                        {{ strtoupper(str_replace(['.', '_', '-'], ' ', $group)) }} PERMISSION
                                    </span>
                                    <div class="perm-card-actions">
                                        <span class="perm-action-btn select-group-btn" data-group="{{ $group }}" title="Select All">
                                            <i class="fa fa-check"></i>
                                        </span>
                                        <span class="perm-action-btn reset reset-group-btn" data-group="{{ $group }}" title="Deselect All">
                                            <i class="fa fa-refresh"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="perm-card-body">
                                    @foreach($groupPerms as $perm)
                                    <label class="perm-item">
                                        <input type="checkbox"
                                            name="permissions[]"
                                            value="{{ $perm->name }}"
                                            data-group="{{ $group }}"
                                            {{ in_array($perm->name, $rolePermissions) ? 'checked' : '' }}>
                                        {{ ucfirst(str_replace(['.', '_'], ' ', last(explode('.', $perm->name, 2)))) }}
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="text-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fa fa-floppy-o me-1"></i> {{ $btnText }}
                    </button>
                    <a href="{{ route('role.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    document.querySelectorAll('.select-group-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const group = this.dataset.group;
            document.querySelectorAll(`input[data-group="${group}"]`)
                .forEach(b => b.checked = true);
        });
    });

    document.querySelectorAll('.reset-group-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const group = this.dataset.group;
            document.querySelectorAll(`input[data-group="${group}"]`)
                .forEach(b => b.checked = false);
        });
    });

    document.getElementById('selectAllBtn').addEventListener('click', function () {
        document.querySelectorAll('input[data-group]').forEach(b => b.checked = true);
    });

    document.getElementById('deselectAllBtn').addEventListener('click', function () {
        document.querySelectorAll('input[data-group]').forEach(b => b.checked = false);
    });
</script>
@endpush