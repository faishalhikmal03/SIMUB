<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jawaban;
use App\Models\Kuesioner;
use App\Models\StatusPengisian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// PERBAIKAN: Nama kelas diubah menjadi JawabanController
class JawabanController extends Controller
{
    /**
     * Menampilkan halaman manajemen jawaban dengan filter dan pagination.
     * Logika mengambil data per sesi pengisian (submission).
     */
    public function index(Request $request)
    {
        $kuesioners = Kuesioner::orderBy('judul')->get();
        
        $query = $this->getFilteredSubmissionsQuery($request);
        
        $hasilPengisian = $query->latest('waktu_pengisian')->paginate(10);

        return view('admin.jawaban.index', [
            'hasilPengisian' => $hasilPengisian,
            'kuesioners' => $kuesioners,
            'filters' => $request->only(['kuesioner_id', 'role']),
        ]);
    }

    /**
     * Menampilkan detail jawaban dari satu sesi pengisian.
     */
    public function show($submissionUuid)
    {
        $jawabanPertama = Jawaban::where('submission_uuid', $submissionUuid)->with(['user', 'kuesioner'])->firstOrFail();
        
        $kuesionerAnswers = Jawaban::where('submission_uuid', $submissionUuid)
            ->with(['section', 'pertanyaan', 'pilihanJawaban'])
            ->get();
            
        return view('admin.jawaban.show', [
            'user' => $jawabanPertama->user,
            'kuesioner' => $jawabanPertama->kuesioner,
            'submissionUuid' => $submissionUuid,
            'kuesionerAnswers' => $kuesionerAnswers
        ]);
    }

    /**
     * Menghapus semua data jawaban yang terkait dengan satu sesi pengisian.
     */
    public function destroy($submissionUuid)
    {
        DB::beginTransaction();
        try {
            $jawabanDihapus = Jawaban::where('submission_uuid', $submissionUuid)->delete();

            if($jawabanDihapus == 0) {
                return back()->with('error', 'Data jawaban tidak ditemukan.');
            }
            
            DB::commit();
            return redirect()->route('admin.jawaban.index')->with('success', 'Data sesi jawaban berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus jawaban: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus data jawaban.');
        }
    }

    /**
     * Mengekspor ringkasan data jawaban yang sudah difilter ke dalam file CSV.
     */
    public function export(Request $request)
    {
        $hasilPengisian = $this->getFilteredSubmissionsQuery($request)->get();
        
        $fileName = 'ringkasan_jawaban_kuesioner_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($hasilPengisian) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID Sesi', 'Nama Pengisi', 'Role', 'NPM/NIDN/Yudisium', 'Judul Kuesioner', 'Waktu Pengisian']);

            foreach ($hasilPengisian as $hasil) {
                $user = $hasil->user;
                $identifier = '';
                if(in_array($user->role, ['mahasiswa', 'mahasiswa_baru'])) {
                    $identifier = (string)($user->npm ?? 'N/A');
                } elseif($user->role == 'dosen') {
                    $identifier = (string)($user->nidn ?? 'N/A');
                } elseif($user->role == 'alumni') {
                    $identifier = $user->tanggal_yudisium ? \Carbon\Carbon::parse($user->tanggal_yudisium)->format('Y-m-d') : 'N/A';
                }

                fputcsv($file, [
                    $hasil->submission_uuid,
                    $user->nama,
                    $user->role,
                    $identifier,
                    $hasil->kuesioner->judul,
                    \Carbon\Carbon::parse($hasil->waktu_pengisian)->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

   /**
     * Mengekspor jawaban detail dari satu sesi pengisian.
     */
    public function exportDetail($submissionUuid)
    {
        $answers = Jawaban::where('submission_uuid', $submissionUuid)
            ->with(['kuesioner', 'user', 'section', 'pertanyaan', 'pilihanJawaban'])
            ->get();

        if ($answers->isEmpty()) {
            abort(404, 'Data jawaban untuk sesi ini tidak ditemukan.');
        }

        $kuesioner = $answers->first()->kuesioner;
        $user = $answers->first()->user;

        $fileName = 'jawaban_detail-' . Str::slug($user->nama) . '-' . Str::limit(Str::slug($kuesioner->judul), 20) . '.csv';
        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($kuesioner, $user, $answers) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['sep=,']);
            fputcsv($file, ['Judul Kuesioner:', $kuesioner->judul]);
            fputcsv($file, ['Diisi oleh:', $user->nama]);
            fputcsv($file, []); // Baris kosong
            fputcsv($file, ['Section', 'Pertanyaan', 'Jawaban']);

            $answerMap = [];
            foreach ($answers as $answer) {
                if ($answer->jawaban_text) {
                    $answerMap[$answer->pertanyaan_id][] = $answer->jawaban_text;
                } elseif ($answer->pilihanJawaban) {
                    $answerMap[$answer->pertanyaan_id][] = $answer->pilihanJawaban->pilihan;
                }
            }

            // Load relasi sections dan pertanyaans pada kuesioner
            $kuesioner->load('sections.pertanyaans');

            foreach ($kuesioner->sections as $section) {
                foreach ($section->pertanyaans as $pertanyaan) {
                    $jawabanText = $answerMap[$pertanyaan->id] ?? ['TIDAK DIJAWAB'];
                    
                    fputcsv($file, [
                        $section->judul,
                        $pertanyaan->pertanyaan,
                        implode('; ', $jawabanText)
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Metode privat untuk membangun query filter agar tidak duplikasi kode.
     * Logika mengambil data per sesi pengisian.
     */
    private function getFilteredSubmissionsQuery(Request $request)
    {
        $query = Jawaban::select(
                'submission_uuid', 
                'user_id', 
                'kuesioner_id', 
                DB::raw('MAX(created_at) as waktu_pengisian')
            )
            ->whereNotNull('submission_uuid')
            ->with(['user', 'kuesioner'])
            ->groupBy('submission_uuid', 'user_id', 'kuesioner_id');

        if ($request->filled('kuesioner_id')) {
            $query->where('kuesioner_id', $request->kuesioner_id);
        }

        if ($request->filled('role')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }
        
        return $query;
    }
}

