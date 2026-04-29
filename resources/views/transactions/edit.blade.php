@extends('layouts.app')
@section('title', __('app.edit_transaction'))
@section('page-title', __('app.edit_transaction'))

@section('content')
<div class="max-w-lg mx-auto bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg text-sm text-red-700 dark:text-red-400">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-4">
        @csrf @method('PUT')

        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.all_types') }}</label>
            <div class="mt-1">
                <x-select name="type" :selected="$transaction->type" :options="[
                    ['value'=>'expense','label'=>__('app.expense')],
                    ['value'=>'income', 'label'=>__('app.income')],
                ]"/>
            </div>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.amount') }}</label>
            <input type="number" name="amount" step="0.01" min="0.01" value="{{ $transaction->amount }}" required
                   class="mt-1 w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.category') }}</label>
            <div class="mt-1">
                <x-select name="category_id"
                    :selected="$transaction->category_id ?? ''"
                    :placeholder="'— '.__('app.optional').' —'"
                    :options="collect([['value'=>'','label'=>'— '.__('app.optional').' —']])->concat($categories->map(fn($c)=>['value'=>$c->id,'label'=>$c->localeName()]))->toArray()"/>
            </div>
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.description') }}</label>
            <input type="text" name="description" value="{{ $transaction->description }}"
                   class="mt-1 w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
        </div>
        <div>
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('app.date') }}</label>
            <input type="date" name="date" value="{{ $transaction->date->format('Y-m-d') }}" required
                   class="mt-1 w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:outline-none text-sm">
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('transactions.index') }}" class="flex-1 py-2.5 rounded-xl border border-gray-200 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 text-center hover:bg-gray-50 dark:hover:bg-gray-700 transition">{{ __('app.cancel') }}</a>
            <button type="submit" class="flex-1 py-2.5 rounded-xl bg-green-600 text-white text-sm font-semibold hover:bg-green-700 transition">{{ __('app.save') }}</button>
        </div>
    </form>
</div>
@endsection
