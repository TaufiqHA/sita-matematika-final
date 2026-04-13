<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'mahasiswa_id',
    'jenis_seminar',
    'status',
    'catatan_tu',
])]
class PendaftaranSeminar extends Model
{
    use HasFactory;

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }

    public function berkas(): HasMany
    {
        return $this->hasMany(BerkasSeminar::class, 'pendaftaran_id');
    }
}
