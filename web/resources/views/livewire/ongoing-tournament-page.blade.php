@php use App\Models\Enums\EventStatus; @endphp
<div>
    <x-navigation/>
    <div class="px-4 pb-4">
        <h1 class="text-3xl mt-2">{{ $tournamentData->name }}</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-2 mb-5">{{ $tournamentData->description }}</p>
        {{ $this->table }}
    </div>
</div>
