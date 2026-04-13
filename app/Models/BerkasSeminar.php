<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'pendaftaran_id',
    'nama_berkas',
    'file_path',
    'status_verifikasi',
])]
class BerkasSeminar extends Model
{
    use HasFactory;

    public function pendaftaran(): BelongsTo
    {
        return $this->belongsTo(PendaftaranSeminar::class, 'pendaftaran_id');
    }
}
