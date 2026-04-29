{{--
  Custom Alpine.js select component — replaces native <select>.
  Props:
    $name        — form field name
    $options     — array of ['value' => ..., 'label' => ...]
    $selected    — currently selected value (string)
    $placeholder — placeholder text when nothing selected
--}}
@props([
    'name',
    'options' => [],
    'selected' => '',
    'placeholder' => '—',
])

<div x-data="{
    open: false,
    selected: '{{ addslashes($selected) }}',
    options: {{ Js::from($options) }},
    get label() {
        const opt = this.options.find(o => String(o.value) === String(this.selected));
        return opt ? opt.label : '{{ addslashes($placeholder) }}';
    }
}" class="relative" @keydown.escape="open=false" @click.outside="open=false">

    <input type="hidden" name="{{ $name }}" :value="selected">

    <button type="button"
            @click="open=!open"
            :class="open ? 'ring-2 ring-green-500 border-green-500' : 'border-gray-200 dark:border-gray-600'"
            class="w-full flex items-center justify-between gap-2
                   px-3 py-1.5 rounded-xl border text-sm text-left
                   bg-white dark:bg-gray-700
                   text-gray-800 dark:text-gray-100
                   hover:border-gray-300 dark:hover:border-gray-500
                   focus:outline-none transition-colors cursor-pointer">
        <span class="truncate" x-text="label"></span>
        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 transition-transform duration-150"
             :class="open ? 'rotate-180' : ''"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-cloak
         x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-1 w-full min-w-[160px]
                bg-white dark:bg-gray-800
                border border-gray-200 dark:border-gray-700
                rounded-xl shadow-lg overflow-hidden">
        <ul class="max-h-52 overflow-y-auto py-1">
            <template x-for="opt in options" :key="opt.value">
                <li @click="selected = opt.value; open = false"
                    :class="String(selected) === String(opt.value)
                        ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 font-medium'
                        : 'text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700'"
                    class="px-3 py-2 text-sm cursor-pointer flex items-center gap-2 transition-colors">
                    <span class="w-3.5 flex-shrink-0">
                        <svg x-show="String(selected) === String(opt.value)"
                             class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span x-text="opt.label" class="truncate"></span>
                </li>
            </template>
        </ul>
    </div>
</div>
