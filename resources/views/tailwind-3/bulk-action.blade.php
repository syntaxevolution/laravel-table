<li wire:key="{{ Str::of($bulkAction->identifier)->snake('-')->slug() }}">
    <button wire:click.prevent="bulkAction('{{ $bulkAction->identifier }}', {{ $bulkAction->getConfirmationQuestion() ? 1 : 0 }})"
            class="block w-full py-1 px-6 font-normal text-gray-900 whitespace-no-wrap border-0"
            title="{{ $label }}"
            type="button">
        {{ $label }}
    </button>
</li>
