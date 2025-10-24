<?php
namespace App\Repositories;

use App\Models\Message;

class MessageRepository
{
    public function create(array $data): Message
    {
        return Message::create($data);
    }

    public function findById(int $id): ?Message
    {
        return Message::find($id);
    }

    public function all(int $userId){
        return Message::where('receiver_id', $userId)->latest();
    }

    public function update(int $id, array $data): bool
    {
        $message = Message::find($id);
        if (!$message) {
            return false;
        }

        return $message->update($data);
    }

    public function delete(int $id): bool
    {
        $message = Message::find($id);
        if (!$message) {
            return false;
        }

        return $message->delete();
    }

    //rechercher les messages non lus
    public function getUnreadByUser(int $userId)
    {
        return Message::where('receiver_id', $userId)
                      ->where('is_read', false)
                      ->latest()
                      ->get();
    }

    //lire un message
    public function markAsRead(int $id): bool
    {
        $message = Message::find($id);
        if (!$message) {
            return false;
        }

        $message->is_read = true;
        return $message->save();
    }
}