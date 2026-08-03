<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use App\Services\ManagerService;
use App\Services\UserService;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $managerService;
    protected $userService;

    public function __construct(PaymentService $paymentService, ManagerService $managerService, UserService $userService)
    {
        $this->paymentService = $paymentService;
        $this->managerService = $managerService;
        $this->userService = $userService;


    }

    //index (paiements et recus sur la mm vue) 
    public function index(Request $request)
    {
        $payments = $this->paymentService->getAllPayments();

        return view('payments.index', compact('payments'));
    }

    public function indexReceipts(Request $request)
    {
        $receipts = $this->paymentService->getAllReceipts();

        return view('payments.indexRec', compact('receipts'));
    }
    //create
    public function create()
    {
        $user=Auth::user();
        $managers=$this->userService->managers();

        if($user->role=='gestionnaire'){
            $tenants=$this->managerService->locatairesAvecContratActifByManager($user->id);
        }else{
            $tenants=$this->userService->getLocatairesAvecContratActif();
        }
      
        $paymentMethods = $this->paymentService->getPaymentMethods();

        return view('payments.create', compact('tenants', 'paymentMethods', 'managers'));
    }
    //store
    public function store(Request $request)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'manager_id' => 'required|exists:users,id',
            'payment_method_id' => 'nullable',
            'create_payment_method' => 'nullable|in:yes,no',
            'amount' => 'required|numeric|min:0',
            'motif' => 'required|in:loyer,caution,reparation,autre',
        ]);

        $payment = $this->paymentService->createCashPayment($data);
        if (!$payment) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du paiement.');
        }
        return redirect()->route('payments.index')->with('success', 'Paiement créé avec succès.');
    }
    //form generate receipt
    public function formGenerateReceipt($paymentId)
    {
        $payment = $this->paymentService->getPaymentById($paymentId);
        return view('payments.genRec', compact('payment'));
    }
    //generate receipt
    public function generateReceipt(Request $request, int $paymentId)
    {
        try {
            $validated = $request->validate([
                'periods' => 'array',
                'periods.*.period_start' => 'required|date',
                'periods.*.period_end' => 'required|date|after_or_equal:periods.*.period_start',
            ]);

            $user = Auth::user();

            $receipt = $this->paymentService->generateReceipt(
                $paymentId,
                $validated['periods'],
                $user->id
            );

            return redirect()->route('receipts.show', $receipt->id)
                ->with('success', 'Reçu généré avec succès.');

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }
    }
    //edit receipt
    public function editReceipt($id)
    {
        $receipt = $this->paymentService->getReceiptById($id);
        $periods = $this->paymentService->getReceiptPeriods($id);
        return view('payments.editRec', compact('receipt', 'periods'));
    }
    //update receipt
    public function updateReceipt(Request $request, $id)
    {
        $data = $request->validate([
            'periods.*.period_start' => 'required|date',
            'periods.*.period_end' => 'required|date|after_or_equal:periods.*.period_start',
        ]);

        $updated = $this->paymentService->updateReceipt($id, $data);
        if (!$updated) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour du reçu.');
        }
        return redirect()->route('receipts.index')->with('success', 'Reçu mis à jour avec succès.');
    }
    //edit payment
    public function editPayment($id)
    {
        $payment = $this->paymentService->getPaymentById($id);
        $user=Auth::user();
        $managers=$this->userService->managers();

        if($user->role=='gestionnaire'){
            $tenants=$this->managerService->allLocatairesByManager($user->id);
        }else{
            $tenants=$this->userService->getLocataires();
        }

        $paymentMethods = $this->paymentService->getPaymentMethods();

        return view('payments.editPay', compact('payment', 'tenants', 'paymentMethods', 'managers'));
    }
    //update payment
    public function updatePayment(Request $request, $id)
    {
        $data = $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'manager_id' => 'required|exists:users,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $updated = $this->paymentService->updatePayment($id, $data);
        if (!$updated) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour du paiement.');
        }
        return redirect()->route('payments.index')->with('success', 'Paiement mis à jour avec succès.');
    }
    //destroy receipt
    public function destroyReceipt($id)
    {
        $deleted = $this->paymentService->deleteReceipt($id);
        if (!$deleted) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression du reçu.');
        }
        return redirect()->route('receipts.index')->with('success', 'Reçu supprimé avec succès.');
    }
    //destroy payment
    public function destroyPayment($id)
    {
        $deleted = $this->paymentService->deletePayment($id);
        if (!$deleted) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression du paiement.');
        }
        return redirect()->route('payments.index')->with('success', 'Paiement supprimé avec succès.');
    }
    //show paiement
    public function showPayment($id)
    {
        $payment = $this->paymentService->getPaymentById((int) $id);
        $this->authorizeOwnership($payment->tenant_id);
        return view('payments.showPay', compact('payment'));
    }
    //show receipt
    public function showReceipt($id)
    {
        $receipt = $this->paymentService->getReceiptById((int) $id);
        $this->authorizeOwnership($receipt->tenant_id);
        return view('payments.showRec', compact('receipt'));
    }

    /**
     * Un locataire ne peut consulter que ses propres paiements/reçus.
     * Admin et gestionnaire ont un accès complet.
     */
    private function authorizeOwnership(int $tenantId): void
    {
        $user = Auth::user();
        if ($user->role === 'locataire' && $user->id !== $tenantId) {
            abort(403, "Vous ne pouvez pas consulter les paiements d'un autre locataire.");
        }
    }
    //send demande
    public function sendPaymentRequest(Request $request)
    {
        $user = Auth::id();
        $sent = $this->paymentService->requestCashPaymentAppointment($user);
        if (!$sent) {
            return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'envoi de la demande de paiement.');
        }
        return redirect()->route('payments.index')->with('success', 'Demande de paiement envoyée avec succès.');
    }
    //search payment
    public function searchPayment(Request $request)
    {
        $term = $request->query('q');
        $payments = $this->paymentService->searchPayments($term);

        return view('payments.index', compact('payments'));
    }
    //search receipt
    public function searchReceipts(Request $request)
    {
        $term = $request->query('q');
        $receipts = $this->paymentService->searchReceipts($term);
        return view('payments.indexRec', compact('receipts'));
    }

}