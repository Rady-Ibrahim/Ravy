@extends('layouts.admin')

@section('title', __('Edit variant'))
@section('page_title', __('Edit variant'))
@section('page_subtitle', $product->name.' - '.$variant->sku)

@section('content')
    <div class="mx-auto max-w-4xl">
        <form action="{{ route('admin.products.variants.update', [$product, $variant]) }}" method="post" class="admin-card space-y-5">
            @csrf
            @method('PUT')
            @include('product::admin.variants.partials.form', ['variant' => $variant])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Save changes') }}</button>
                <a href="{{ route('admin.products.variants.index', $product) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
