@extends('layouts.admin')

@section('title', __('Create attribute'))
@section('page_title', __('Create attribute'))
@section('page_subtitle', __('Add new product attribute (color, size...).'))

@section('content')
    <div class="mx-auto max-w-3xl">
        <form action="{{ route('admin.attributes.store') }}" method="post" class="admin-card space-y-5">
            @csrf
            @include('product::admin.attributes.partials.form', ['attribute' => null])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Create') }}</button>
                <a href="{{ route('admin.attributes.index') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
