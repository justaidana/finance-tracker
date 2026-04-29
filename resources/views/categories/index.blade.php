@extends('layouts.app')
@section('title', __('app.categories'))
@section('page-title', __('app.categories'))

@section('content')

{{-- Add New Category --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-5 mb-5" x-data="{ open: false }">
    <div class="flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-800 dark:text-white">{{ __('app.add_category') }}</h2>
        <button @click="open=!open"
                class="flex items-center gap-2 px-4 py-2 rounded-xl bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition">
            @include('components.icon', ['name' => 'plus', 'class' => 'w-4 h-4'])
            {{ __('app.add_category') }}
        </button>
    </div>

    <form x-show="open" x-transition method="POST" action="{{ route('categories.store') }}"
          class="mt-4 grid grid-cols-2 sm:grid-cols-4 gap-3" style="display:none">
        @csrf
        <div class="col-span-2 sm:col-span-1">
            <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">{{ __('app.name') }}</label>
            <input type="text" name="name" required maxlength="100"
                   class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
        </div>
        <div>
            <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">{{ __('app.all_types') }}</label>
            <x-select name="type" selected="expense" :options="[
                ['value'=>'expense','label'=>__('app.expense')],
                ['value'=>'income', 'label'=>__('app.income')],
            ]"/>
        </div>
        <div>
            <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">{{ __('app.color') }}</label>
            <input type="color" name="color" value="#1D9E75"
                   class="w-full h-9 rounded-xl border border-gray-200 dark:border-gray-600 cursor-pointer px-1">
        </div>
        <div>
            <label class="text-xs font-medium text-gray-600 dark:text-gray-400 mb-1 block">{{ __('app.icon') }}</label>
            <x-select name="icon" selected="tag" :options="[
                ['value'=>'tag','label'=>'Tag'],
                ['value'=>'receipt','label'=>'Receipt'],
                ['value'=>'chart-bar','label'=>'Chart'],
                ['value'=>'trending','label'=>'Trending'],
                ['value'=>'book','label'=>'Book'],
                ['value'=>'piggy','label'=>'Savings'],
                ['value'=>'check','label'=>'Check'],
            ]"/>
        </div>
        <div class="col-span-2 sm:col-span-4">
            <button type="submit" class="w-full py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition">
                {{ __('app.save') }}
            </button>
        </div>
    </form>

    @if($errors->any())
    <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg text-sm text-red-700 dark:text-red-400">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif
</div>

{{-- Expense Categories --}}
<div class="mb-5">
    <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">{{ __('app.expense') }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($categories->where('type','expense') as $cat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 flex items-center gap-3 relative"
             x-data="{ editing: false }">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: {{ $cat->color }}22">
                @include('components.icon', ['name' => $cat->icon ?? 'tag', 'class' => 'w-5 h-5'])
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 dark:text-gray-200 text-sm truncate">{{ $cat->localeName() }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400">{{ __('app.expense') }}</span>
            </div>
            <div class="flex gap-1">
                <button @click="editing=!editing" class="p-1.5 rounded-lg text-gray-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 transition">
                    @include('components.icon', ['name' => 'pencil', 'class' => 'w-4 h-4'])
                </button>
                <button onclick="confirmDelete('{{ route('categories.destroy', $cat) }}', '{{ __('app.delete_category') }}')"
                        class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                    @include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4'])
                </button>
            </div>

            {{-- Inline edit dropdown --}}
            <form x-show="editing" x-transition method="POST" action="{{ route('categories.update', $cat) }}"
                  class="absolute top-full left-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-xl shadow-lg p-4 z-10 w-72"
                  style="display:none" @click.outside="editing=false">
                @csrf @method('PUT')
                <div class="space-y-3">
                    <input type="text" name="name" value="{{ $cat->localeName() }}" required
                           class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                    <div class="flex gap-2">
                        <input type="color" name="color" value="{{ $cat->color }}"
                               class="w-10 h-9 rounded-xl border border-gray-200 dark:border-gray-600 cursor-pointer p-1 flex-shrink-0">
                        <x-select name="icon" :selected="$cat->icon ?? 'tag'" class="flex-1" :options="[
                            ['value'=>'tag','label'=>'Tag'],
                            ['value'=>'receipt','label'=>'Receipt'],
                            ['value'=>'chart-bar','label'=>'Chart'],
                            ['value'=>'trending','label'=>'Trending'],
                            ['value'=>'book','label'=>'Book'],
                            ['value'=>'piggy','label'=>'Savings'],
                            ['value'=>'check','label'=>'Check'],
                        ]"/>
                    </div>
                    <button type="submit" class="w-full py-2 rounded-xl bg-green-600 text-white text-sm font-medium hover:bg-green-700">{{ __('app.save') }}</button>
                </div>
            </form>
        </div>
        @endforeach
    </div>
</div>

{{-- Income Categories --}}
<div>
    <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">{{ __('app.income') }}</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
        @foreach($categories->where('type','income') as $cat)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: {{ $cat->color }}22">
                @include('components.icon', ['name' => $cat->icon ?? 'tag', 'class' => 'w-5 h-5'])
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium text-gray-800 dark:text-gray-200 text-sm truncate">{{ $cat->localeName() }}</p>
                <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400">{{ __('app.income') }}</span>
            </div>
            <button onclick="confirmDelete('{{ route('categories.destroy', $cat) }}', '{{ __('app.delete_category') }}')"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                @include('components.icon', ['name' => 'trash', 'class' => 'w-4 h-4'])
            </button>
        </div>
        @endforeach
    </div>
</div>

@if($categories->isEmpty())
<div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 shadow-sm">
    <p class="text-gray-400 text-sm">{{ __('app.no_categories') }}</p>
</div>
@endif
@endsection
