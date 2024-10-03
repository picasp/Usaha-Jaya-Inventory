<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OpnameItem extends Model
{
    use HasFactory;
    public function barang(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function opname(): BelongsTo
    {
        return $this->belongsTo(Opname::class);
    }
}
