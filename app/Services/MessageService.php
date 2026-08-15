<?php
namespace App\Services;


use Illuminate\Validation\ValidationException;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\Notifications\NewMessageNotification;
use App\Events\NewNotificationEvent;
use App\Services\WebPushService;
use Illuminate\Support\Facades\Log;

class MessageService{
    protected $messageRepository;
    protected $userRepository;
    protected $webPushService;

    public function __construct(MessageRepository $messageRepository, UserRepository $userRepository, WebPushService $webPushService
    ) {
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
        $this->webPushService = $webPushService;
    }

    //Creer un message
    public function create(array $data)
    {
        $message = $this->messageRepository->create($data);
        $this->notifyReceiver(
            (int) $data['receiver_id'],
            $data['title'] ?? 'Nouveau message',
            $data['content'] ?? null
        );
        return $message;
    }

    //envoyer un message aux admins et gestionnaires
    public function sendToAdminsAndManagers($senderId, $title, $content)
    {
        // Récupérer les IDs des utilisateurs avec les rôles 'admin' et 'gestionnaire'
        $receivers = $this->userRepository->findByIdByAdminAndManagers();

        foreach ($receivers as $receiver) {
            $message = $this->messageRepository->create([
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id,
                'title' => $title,
                'content' => $content,
            ]);

            $receiver->notify(new NewMessageNotification($message));
            $this->notifyReceiver(
                $receiver->id,
                $title,
                $content
            );
        }
    }

    //lire un message
    public function readMessage(int $id): bool
    {
        return $this->messageRepository->markAsRead($id);
    }

    //afficher les messages non lus d'un utilisateur
    public function getUnreadMessagesForUser(int $userId)
    {
        return $this->messageRepository->getUnreadByUser($userId);
    }

    //supprimer un message
    public function deleteMessage(int $id): bool
    {
        return $this->messageRepository->delete($id);
    }

    //afficher tous les messages
    public function getAllMessages(int $userId)
    {
        return $this->messageRepository->all($userId)->paginate(10);
    }

    //consulter un message par son id
    public function getMessageById(int $id)
    {
        return $this->messageRepository->findById($id);
    }

    //envoie de la demande du locataire
    public function sendTenantRequest(int $id,array $data)
    {
        $tenant=$this->userRepository->findById($id);
        $receivers = $this->userRepository->getAdminsAndTenantManager($tenant);

        foreach ($receivers as $receiver) {
            $message = $this->messageRepository->create([
                'sender_id'   => $tenant->id,
                'receiver_id' => $receiver->id,
                'title'       => e($data['title']),
                'content'     => e($data['content']),
                'is_read'     => false
            ]);

            $receiver->notify(new NewMessageNotification($message));
            $this->notifyReceiver(
                $receiver->id,
                $data['title'],
                $data['content'] ?? null
            );
        }
    }

    //notifie un locataire precis (in-app + mail), ex: paiement enregistre par le gestionnaire
    public function notifyTenant(int $tenantId, int $senderId, string $title, string $content): void
    {
        $tenant = $this->userRepository->findById($tenantId);
        if (!$tenant) {
            return;
        }

        $message = $this->messageRepository->create([
            'sender_id' => $senderId,
            'receiver_id' => $tenant->id,
            'title' => e($title),
            'content' => e($content),
            'is_read' => false,
        ]);

        $tenant->notify(new NewMessageNotification($message));
        $this->notifyReceiver(
            $tenant->id,
            $title,
            $content
        );
    }

    private function notifyReceiver(
        int $receiverId,
        string $title,
        ?string $body = null
    ): void {
        $unreadCount = $this->messageRepository
            ->getUnreadByUser($receiverId)
            ->count();

        // 1. Notification temps réel dans l'application    → Reverb → Echo → Livewire
        
        try {
            event(new NewNotificationEvent(
                $receiverId,
                $title,
                $unreadCount
            ));
        } catch (\Throwable $e) {
            Log::error('Erreur diffusion Reverb', [
                'receiver_id' => $receiverId,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }

        // 2. Notification système → Web Push → Service Worker → OS
        try {
            $this->webPushService->sendToUser(
                $receiverId,
                $title,
                $body ?? 'Vous avez une nouvelle notification.',
                '/messages'
            );
        } catch (\Throwable $e) {
            Log::error('Erreur Web Push', [
                'receiver_id' => $receiverId,
                'title' => $title,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
