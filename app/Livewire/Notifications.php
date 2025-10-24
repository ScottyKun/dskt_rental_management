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

    //vers la page de consult
    public function viewMessage($id)
    {
        // Ferme le dropdown côté Alpine
        $this->dispatch('messageRedirecting');

        // Redirige le navigateur via JS
        $this->dispatchBrowserEvent('redirectTo', [
            'url' => route('messages.consult', $id),
        ]);
    }



    //marque comme lu
    public function markAsRead($id)
    {
        $this->messageService->readMessage($id);
        $this->loadMessages(); // rafraîchir la liste
        $this->emit('messageRead'); // event si besoin côté JS
    }

    //charger les messages non lus
    public function loadMessages()
    {
        $user = Auth::user();
        $this->messages = $user ? $this->messageService->getUnreadMessagesForUser($user->id) : collect();
    }
}
