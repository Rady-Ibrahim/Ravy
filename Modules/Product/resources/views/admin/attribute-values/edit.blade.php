@extends('layouts.admin')

@section('title', __('Edit value'))
@section('page_title', __('Edit value'))
@section('page_subtitle', $attribute->name.' - '.$value->value)

@section('content')
    <div class="mx-auto max-w-3xl">
        <form action="{{ route('admin.attributes.values.update', [$attribute, $value]) }}" method="post" class="admin-card space-y-5">
            @csrf
            @method('PUT')
            @include('product::admin.attribute-values.partials.form', ['value' => $value])
            <div class="flex items-center gap-3">
                <button type="submit" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">{{ __('Save changes') }}</button>
                <a href="{{ route('admin.attributes.values.index', $attribute) }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
@endsection
