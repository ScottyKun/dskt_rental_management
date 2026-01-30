<?php
namespace App\Repositories;
use Illuminate\Support\Facades\Auth;

use App\Models\Receipt;
use App\Models\ReceiptPeriod;

class ReceiptRepository
{
    //create receipt
    public function create(array $data)
    {
        return Receipt::create($data);
    }
    //update receipt
    public function update(int $id, array $data): bool
    {
        $receipt = Receipt::find($id);
        if (!$receipt) {
            return false;
        }

        return $receipt->update($data);
    }
    //delete receipt
    public function delete(int $id): bool
    {
        $receipt = Receipt::find($id);
        if (!$receipt) {
            return false;
        }

        return $receipt->delete();
    }
    //find by id
    public function findById(int $id): ?Receipt
    {
        return Receipt::find($id);
    }
    //get by tenant
    public function getByTenant(int $tenantId, int $perPage = 10)
    {
        return Receipt::where('tenant_id', $tenantId)->orderBy('created_at', 'desc')->paginate($perPage);
    }
    // get by generator
    public function getByGenerator(int $userId, int $perPage = 10)
    {
        return Receipt::where('generated_by', $userId)->orderBy('created_at', 'desc')->paginate($perPage);
    }
    //search
    public function search(?string $term = null, int $perPage = 10)
    {
        $query = Receipt::with(['tenant', 'generator', 'payment']);

        if ($term) {
            $query->where(function ($q) use ($term) {

                $q->where('receipt_number', 'LIKE', "%{$term}%")
                  ->orWhere('total_amount', 'LIKE', "%{$term}%")
                  ->orWhereHas('tenant', function ($tenantQuery) use ($term) {
                      $tenantQuery->where('name', 'LIKE', "%{$term}%")
                                  ->orWhere('surname', 'LIKE', "%{$term}%");
                  })
                  ->orWhereHas('generator', function ($genQuery) use ($term) {
                      $genQuery->where('name', 'LIKE', "%{$term}%")
                               ->orWhere('surname', 'LIKE', "%{$term}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    //receiptPeriod
    //create receipt period
    public function createReceiptPeriod(array $data)
    {
        return ReceiptPeriod::create($data);
    }
    //getby receipt
    public function getByReceipt(int $receiptId)
    {
        return ReceiptPeriod::where('receipt_id', $receiptId)->get();
    }
    //delete receipt period by receipt
    public function deleteByReceipt(int $receiptId): bool
    {
        return ReceiptPeriod::where('receipt_id', $receiptId)->delete() > 0;
    }


}