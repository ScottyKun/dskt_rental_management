<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\MessageService;
class MessageController extends Controller
{
    protected $messageService;
    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
        $this->middleware('auth');
    }

   

    //afficher tous les messages
    public function index()
    {
        $user = Auth::user();
        $messages = $this->messageService->getAllMessages($user->id);
        return view('messages.index', compact('messages'));
    }

   
    //consulter un message
    public function consult($id){
        $user = Auth::user();
        $message = $this->messageService->getMessageById($id);

        if (!$message) {
            return redirect()->back()->with('error', 'Message not found.');
        }

        return view('messages.consult', compact('message'));
    }

    //lire un message
    public function read($id){
        $message = $this->messageService->readMessage($id); 
        
        if (!$message) {
            return redirect()->back()->with('error', 'Message not found or cannot be marked as read.');
        }

        return redirect()->route('messages.consult',$id)->with('success', 'Message marked as read.');
    }

    //supprimer un message
    public function delete($id)
    {
        $deleted = $this->messageService->deleteMessage($id);

        if (!$deleted) {
            return redirect()->back()->with('error', 'Message not found or could not be deleted.');
        }

        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }
}
