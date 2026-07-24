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

        $this->authorizeAccess($message, $user);

        return view('messages.consult', compact('message'));
    }

    //lire un message
    public function read($id){
        $user = Auth::user();
        $message = $this->messageService->getMessageById($id);

        if (!$message) {
            return redirect()->back()->with('error', 'Message not found or cannot be marked as read.');
        }

        $this->authorizeAccess($message, $user);

        $message = $this->messageService->readMessage($id);

        return redirect()->route('messages.consult',$id)->with('success', 'Message marked as read.');
    }

    //supprimer un message
    public function delete($id)
    {
        $user = Auth::user();
        $message = $this->messageService->getMessageById($id);

        if (!$message) {
            return redirect()->back()->with('error', 'Message not found or could not be deleted.');
        }

        $this->authorizeAccess($message, $user);

        $deleted = $this->messageService->deleteMessage($id);

        if (!$deleted) {
            return redirect()->back()->with('error', 'Message not found or could not be deleted.');
        }

        return redirect()->route('messages.index')->with('success', 'Message deleted successfully.');
    }

    /**
     * Seuls l'expediteur et le destinataire d'un message peuvent le consulter/modifier.
     */
    private function authorizeAccess($message, $user): void
    {
        if ($message->sender_id !== $user->id && $message->receiver_id !== $user->id) {
            abort(403, "Vous n'avez pas accès à ce message.");
        }
    }

    //create
    public function create(){
        return view('messages.create');
    }

    //store and send
    public function store(Request $request){
        $data=$request->validate([
            'content' => 'required|min:5',
            'title' => 'required|min:5'
        ]);

        $id=Auth::user()->id;

        $this->messageService->sendTenantRequest($id,$data);

        return redirect()->route('contrats.index')->with('success', 'Votre demande a été envoyée aux responsables.');
    }

}
