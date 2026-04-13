<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReviewPengajuanJudulRequest;
use App\Http\Requests\StorePengajuanJudulRequest;
use App\Models\PengajuanJudul;
use App\Notifications\SystemNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengajuanJudulController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = PengajuanJudul::with('mahasiswa');

        if ($user->hasRole('Mahasiswa')) {
            $query->where('mahasiswa_id', $user->id);
        } elseif ($user->hasRole('DPA')) {
            // Filter by students supervised by this DPA
            $query->whereHas('mahasiswa.mahasiswa', function ($q) use ($user) {
                $q->where('dpa_id', $user->id);
            });
        }

        return response()->json($query->latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePengajuanJudulRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('file_proposal')) {
            $path = $request->file('file_proposal')->store('proposals', 'private');
            $validated['file_proposal'] = $path;
        }

        $pengajuan = $request->user()->pengajuanJuduls()->create($validated);

        // Notify DPA
        $user = $request->user()->load('mahasiswa.dpa');
        $dpa = $user->mahasiswa?->dpa;
        if ($dpa) {
            $dpa->notify(new SystemNotification(
                'Pengajuan Judul Baru',
                $user->name . ' telah mengajukan judul baru.',
                '/dpa/pengajuan-judul/' . $pengajuan->id
            ));
        }

        return response()->json($pengajuan->load('mahasiswa'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $pengajuan = PengajuanJudul::with('mahasiswa')->findOrFail($id);

        // Optional: Add authorization check here or via Policy

        return response()->json($pengajuan);
    }

    /**
     * Review the specified resource.
     */
    public function review(ReviewPengajuanJudulRequest $request, $id): JsonResponse
    {
        $pengajuan = PengajuanJudul::findOrFail($id);

        $pengajuan->update($request->validated());

        // Notify Mahasiswa
        $mahasiswaUser = $pengajuan->mahasiswa;
        if ($mahasiswaUser) {
            $mahasiswaUser->notify(new SystemNotification(
                'Update Pengajuan Judul',
                'Status pengajuan judul Anda saat ini: ' . $request->status,
                '/mahasiswa/pengajuan-judul'
            ));
        }

        return response()->json($pengajuan->load('mahasiswa'));
    }
}
