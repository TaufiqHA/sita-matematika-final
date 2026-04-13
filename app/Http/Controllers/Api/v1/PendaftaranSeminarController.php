<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePendaftaranSeminarRequest;
use App\Http\Requests\VerifikasiSeminarRequest;
use App\Models\BerkasSeminar;
use App\Models\PendaftaranSeminar;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class PendaftaranSeminarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = PendaftaranSeminar::with(['mahasiswa', 'berkas']);

        if ($user->hasRole('Mahasiswa')) {
            $query->where('mahasiswa_id', $user->id);
        } elseif ($user->hasRole('TU')) {
            // TU sees all, usually starting from 'diajukan'
            $query->orderByRaw("CASE WHEN status = 'diajukan' THEN 0 ELSE 1 END");
        }

        return response()->json($query->latest()->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePendaftaranSeminarRequest $request): JsonResponse
    {
        return DB::transaction(function () use ($request) {
            $pendaftaran = $request->user()->pendaftaranSeminars()->create([
                'jenis_seminar' => $request->jenis_seminar,
            ]);

            foreach ($request->berkas as $item) {
                $path = $item['file']->store('berkas_seminar', 'private');

                $pendaftaran->berkas()->create([
                    'nama_berkas' => $item['nama_berkas'],
                    'file_path' => $path,
                ]);
            }

            // Notify TU
            $usersTU = User::role('TU')->get();
            Notification::send($usersTU, new SystemNotification(
                'Pendaftaran Seminar Baru',
                $request->user()->name . ' mendaftar seminar ' . $request->jenis_seminar,
                '/tu/seminar/verifikasi'
            ));

            return response()->json($pendaftaran->load('berkas'), 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        $pendaftaran = PendaftaranSeminar::with(['mahasiswa', 'berkas'])->findOrFail($id);

        return response()->json($pendaftaran);
    }

    /**
     * Upload additional/revised files.
     */
    public function uploadBerkasTambahan(Request $request, $id): JsonResponse
    {
        $request->validate([
            'nama_berkas' => ['required', 'string', 'max:255'],
            'file' => ['required', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
        ]);

        $pendaftaran = PendaftaranSeminar::findOrFail($id);

        // Optional: Authorization check to ensure only the owner can upload
        if ($request->user()->id !== $pendaftaran->mahasiswa_id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $path = $request->file('file')->store('berkas_seminar', 'private');

        $berkas = $pendaftaran->berkas()->create([
            'nama_berkas' => $request->nama_berkas,
            'file_path' => $path,
        ]);

        return response()->json($berkas, 201);
    }

    /**
     * Verify the registration (Staff TU).
     */
    public function verifikasi(VerifikasiSeminarRequest $request, $id): JsonResponse
    {
        return DB::transaction(function () use ($request, $id) {
            $pendaftaran = PendaftaranSeminar::findOrFail($id);

            $pendaftaran->update([
                'status' => $request->status,
                'catatan_tu' => $request->catatan_tu,
            ]);

            if ($request->has('berkas_status')) {
                foreach ($request->berkas_status as $item) {
                    BerkasSeminar::where('id', $item['id'])
                        ->where('pendaftaran_id', $pendaftaran->id)
                        ->update(['status_verifikasi' => $item['status']]);
                }
            }

            // Notify Mahasiswa
            $mahasiswaUser = $pendaftaran->mahasiswa;
            if ($mahasiswaUser) {
                $mahasiswaUser->notify(new SystemNotification(
                    'Verifikasi Pendaftaran Seminar',
                    'Pendaftaran seminar Anda telah diverifikasi oleh TU dengan status: ' . $request->status,
                    '/mahasiswa/seminar/status'
                ));
            }

            return response()->json($pendaftaran->load('berkas'));
        });
    }
}
