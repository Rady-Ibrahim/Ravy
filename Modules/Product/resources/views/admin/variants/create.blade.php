@extends('layouts.admin')

@section('title', __('Create variant'))
@section('page_title', __('Create variant'))
@section('page_subtitle', $product->name)

@section('content')
    <div class="mx-auto max-w-4xl">
        <form action="{{ route('admin.products.variants.store', $product) }}" method="post" enctype="multipart/form-data" class="admin-card space-y-5">
            @csrf
            @include('product::admin.variants.partials.form', ['variant' => null])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Create') }}</button>
                <a href="{{ route('admin.products.variants.index', $product) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
