<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    public function transacted_items(): HasMany
    {
        return $this->hasMany(TransactedItems::class, 'transaction_id');
    }

    public function getTotalAttribute(){
        return $this->transacted_items()->sum('amount');
    }
}
