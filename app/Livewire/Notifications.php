<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Services\MessageService;

class Notifications extends Component
{
    public $messages; // liste des messages non lus

    protected $messageService;

    public function mount(MessageService $messageService)
    {
        $this->messageService = $messageService;
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.notifications');
    }

    //vers la page de consult (redirection serveur directe, geree nativement par Livewire 3)
    public function viewMessage($id)
    {
        return redirect()->route('messages.consult', $id);
    }

    //marque comme lu
    public function markAsRead($id)
    {
        $this->messageService->readMessage($id);
        $this->loadMessages(); // rafraîchir la liste
        $this->dispatch('messageRead'); // event Livewire 3 (remplace emit(), supprime en v3)
    }

    //charger les messages non lus
    public function loadMessages()
    {
        $user = Auth::user();
        $this->messages = $user ? $this->messageService->getUnreadMessagesForUser($user->id) : collect();
    }
}
