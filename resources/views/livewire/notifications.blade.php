<div class="relative">
    <!-- Icône cloche -->
    <button type="button" wire:click.stop="toggleDropdown" onclick="window.enablePushIfNeeded?.()" class="relative focus:outline-none">
        <i class="fa-solid fa-bell text-xl"></i>
        @if($messages->count() > 0)
            <span class="absolute top-0 right-0 inline-block w-3 h-3 bg-red-500 rounded-full"></span>
        @endif
    </button>

    <!-- Dropdown notifications -->
    @if($open)
    <div class="absolute right-0 mt-2 w-80 bg-white shadow-lg rounded-lg overflow-hidden z-50">
        @forelse($messages as $msg)
            <div class="flex justify-between items-start px-4 py-2 hover:bg-gray-100">
                <!-- Lien vers le message -->
                <div
                    class="flex-1 cursor-pointer"
                    wire:click="viewMessage({{ $msg->id }})"
                    wire:loading.attr="disabled"
                >
                    <strong class="block text-gray-800">{{ $msg->title }}</strong>
                    <p class="text-sm text-gray-600 line-clamp-2">{{ $msg->content }}</p>
                    <small class="text-xs text-gray-400">
                        {{ $msg->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>

                <!-- Bouton marquer comme lu -->
                <button
                    type="button"
                    wire:click="markAsRead({{ $msg->id }})"
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
    @endif
</div>
@auth
<script>
document.addEventListener('DOMContentLoaded', function () {
    const waitForEcho = setInterval(function () {
        if (!window.Echo) {
            return;
        }

        clearInterval(waitForEcho);

        window.Echo
            .private('user.{{ auth()->id() }}')
            .listen('.notification.new', function (data) {
                console.log('Nouvelle notification reçue :', data);

                Livewire.dispatch('notificationReceived');
            });

    }, 200);
});
</script>
@endauth