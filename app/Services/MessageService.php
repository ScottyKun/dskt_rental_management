<?php
namespace App\Services;


use Illuminate\Validation\ValidationException;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;

class MessageService{
    protected $messageRepository;
    protected $userRepository;
    public function __construct(MessageRepository $messageRepository, UserRepository $userRepository)
    {
        $this->messageRepository = $messageRepository;
        $this->userRepository = $userRepository;
    }

    //Creer un message
    public function create(array $data)
    {
        return $this->messageRepository->create($data);
    }

    //envoyer un message aux admins et gestionnaires
    public function sendToAdminsAndManagers($senderId, $title, $content)
    {
        // Récupérer les IDs des utilisateurs avec les rôles 'admin' et 'gestionnaire'
        $receivers = $this->userRepository->findByIdByAdminAndManagers();

        foreach ($receivers as $receiver) {
            $this->messageRepository->create([
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id,
                'title' => $title,
                'content' => $content,
            ]);
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
}    