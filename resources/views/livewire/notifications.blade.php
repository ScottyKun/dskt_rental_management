<div class="relative" x-data="{ open: false }">
    <!-- Icône cloche -->
    <button @click="open = !open" class="relative focus:outline-none">
        <i class="fa-solid fa-bell text-xl"></i>
        @if($messages->count() > 0)
            <span class="absolute top-0 right-0 inline-block w-3 h-3 bg-red-500 rounded-full"></span>
        @endif
    </button>

    <!-- Dropdown notifications -->
    <div 
        x-show="open" 
        @click.away="open = false"
        x-transition
        class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg overflow-hidden z-50"
    >
        @forelse($messages as $msg)
            <div class="flex justify-between items-start px-4 py-2 hover:bg-gray-100">
                <!-- Lien vers le message -->
                <div 
                    class="flex-1 cursor-pointer"
                    wire:click="viewMessage({{ $msg->id }})">
                    <strong class="block text-gray-800">{{ $msg->title }}</strong>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $msg->content }}</p>
                    <small class="text-xs text-gray-400">
                        {{ $msg->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>

                <!-- Bouton marquer comme lu -->
                <button 
                    wire:click.stop="markAsRead({{ $msg->id }})" 
                    class="text-blue-500 ml-2 hover:text-blue-700"
                    title="Marquer comme lu"
                >
                    <i class="fa-solid fa-check"></i>
                </button>
            </div>
        @empty
            <p class="px-4 py-3 text-gray-500 text-sm text-center">
                Aucune nouvelle notification
            </p>
        @endforelse
    </div>
</div>

<!-- Script Livewire x Alpine -->
<script>
    document.addEventListener('livewire:init', () => {
        // Redirection navigateur
        window.addEventListener('redirectTo', event => {
            window.location.href = event.detail.url;
        });

        // Fermer le menu avant redirection
        Livewire.on('messageRedirecting', () => {
            const dropdown = document.querySelector('[x-data]');
            if (dropdown && dropdown.__x) dropdown.__x.$data.open = false;
        });
    });
</script>
