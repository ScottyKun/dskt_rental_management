<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\MessageService;
use Livewire\Attributes\On;

class Notifications extends Component
{
    public $messages; // liste des messages non lus
    public $open = false;

    protected ?MessageService $messageService = null;

    public function mount()
    {
        $this->messageService = app(MessageService::class);
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.notifications');
    }

    public function toggleDropdown()
    {
        $this->open = ! $this->open;
    }

    //vers la page de consult (redirection serveur directe, geree nativement par Livewire 3)
    public function viewMessage($id)
    {
        logger()->info('Livewire viewMessage called', ['id' => $id, 'user_id' => Auth::id()]);

        return redirect()->route('messages.consult', $id);
    }

    //marque comme lu
    public function markAsRead($id)
    {
        logger()->info('Livewire markAsRead called', ['id' => $id, 'user_id' => Auth::id()]);

        if (! $this->messageService) {
            $this->messageService = app(MessageService::class);
        }

        $this->messageService->readMessage($id);
        $this->loadMessages(); // rafraîchir la liste
        $this->dispatch('messageRead'); // event Livewire 3 (remplace emit(), supprime en v3)
    }

    //charger les messages non lus
    public function loadMessages()
    {
        if (! $this->messageService) {
            $this->messageService = app(MessageService::class);
        }

        $user = Auth::user();
        $this->messages = $user ? $this->messageService->getUnreadMessagesForUser($user->id) : collect();
    }

    //event
    #[On('notificationReceived')]
    public function notificationReceived()
    {
        $this->loadMessages();
    }
}
