<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'mahasiswa_id',
    'judul',
    'topik',
    'bidang',
    'latar_belakang',
    'file_proposal',
    'status',
    'catatan_dpa',
])]
class PengajuanJudul extends Model
{
    use HasFactory;

    /**
     * Relasi ke tabel users (mahasiswa yang mengajukan)
     */
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mahasiswa_id');
    }
}
