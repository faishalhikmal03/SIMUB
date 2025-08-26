<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jawaban;
use App\Models\Kuesioner;
use App\Models\StatusPengisian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JawabanController extends Controller
{
    /**
     * Menampilkan halaman manajemen jawaban dengan filter dan pagination.
     */
    public function index(Request $request)
    {
        $kuesioners = Kuesioner::orderBy('judul')->get();
        
        $query = $this->getFilteredQuery($request);
        
        $hasilPengisian = $query->latest()->paginate(10);

        return view('admin.jawaban.index', [
            'hasilPengisian' => $hasilPengisian,
            'kuesioners' => $kuesioners,
            'filters' => $request->only(['kuesioner_id', 'role']),
        ]);
    }

    /**
     * Menghapus data pengisian kuesioner oleh seorang pengguna.
     */
    public function destroy(StatusPengisian $statusPengisian)
    {
        DB::beginTransaction();
        try {
            Jawaban::where('user_id', $statusPengisian->user_id)
                   ->where('kuesioner_id', $statusPengisian->kuesioner_id)
                   ->delete();
            
            $statusPengisian->delete();

            DB::commit();
            return redirect()->route('admin.jawaban.index')->with('success', 'Data jawaban berhasil dihapus.');

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
        $hasilPengisian = $this->getFilteredQuery($request)->get();
        
        $fileName = 'jawaban_kuesioner_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($hasilPengisian) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['ID Pengguna', 'Nama Pengisi', 'Role', 'NPM/NIDN/Yudisium', 'Judul Kuesioner', 'Waktu Pengisian']);

            foreach ($hasilPengisian as $hasil) {
                $identifier = '';
                if(in_array($hasil->user->role, ['mahasiswa', 'mahasiswa_baru'])) {
                    $identifier = (string)($hasil->user->npm ?? 'N/A');
                } elseif($hasil->user->role == 'dosen') {
                    $identifier = (string)($hasil->user->nidn ?? 'N/A');
                } elseif($hasil->user->role == 'alumni') {
                    $identifier = $hasil->user->tanggal_yudisium ? \Carbon\Carbon::parse($hasil->user->tanggal_yudisium)->format('Y-m-d') : 'N/A';
                }

                fputcsv($file, [
                    $hasil->user->id,
                    $hasil->user->nama,
                    $hasil->user->role,
                    $identifier,
                    $hasil->kuesioner->judul,
                    $hasil->updated_at->format('Y-m-d H:i:s')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

   /**
     * Mengekspor jawaban detail dari satu pengguna untuk satu kuesioner.
     */
    public function exportDetail(StatusPengisian $statusPengisian)
    {
        $kuesioner = Kuesioner::with('sections.pertanyaans.pilihanJawabans')->find($statusPengisian->kuesioner_id);
        $user = $statusPengisian->user;
        $allUserAnswers = Jawaban::where('user_id', $user->id)
            ->where('kuesioner_id', $kuesioner->id)
            ->get();

        $fileName = 'jawaban-' . Str::slug($user->nama) . '-' . Str::slug($kuesioner->judul) . '.csv';
        $headers = [
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($kuesioner, $user, $allUserAnswers) {
            $file = fopen('php://output', 'w');
            
            // PERBAIKAN 1: Menambahkan 'sep=,' untuk kompatibilitas Excel
            fputcsv($file, ['sep=,']);

            // PERBAIKAN 2: Menambahkan detail identitas pengguna
            fputcsv($file, ['Judul Kuesioner:', $kuesioner->judul]);
            fputcsv($file, ['Diisi oleh:', $user->nama]);
            fputcsv($file, ['Role:', Str::title(str_replace('_', ' ', $user->role))]);

            if(in_array($user->role, ['mahasiswa', 'mahasiswa_baru'])) {
                fputcsv($file, ['NPM:', $user->npm ?? 'N/A']);
            } elseif($user->role == 'dosen') {
                fputcsv($file, ['NIDN:', $user->nidn ?? 'N/A']);
            } elseif($user->role == 'alumni') {
                fputcsv($file, ['Tanggal Yudisium:', $user->tanggal_yudisium ? \Carbon\Carbon::parse($user->tanggal_yudisium)->format('d F Y') : 'N/A']);
            }
            
            fputcsv($file, []); // Baris kosong sebagai pemisah

            // Menulis header untuk tabel jawaban
            fputcsv($file, ['Section', 'Pertanyaan', 'Jawaban']);

            foreach ($kuesioner->sections as $section) {
                foreach ($section->pertanyaans as $pertanyaan) {
                    $answersForThisQuestion = $allUserAnswers->where('pertanyaan_id', $pertanyaan->id);
                    $jawabanText = 'TIDAK DIJAWAB';

                    if ($answersForThisQuestion->isNotEmpty()) {
                        if ($pertanyaan->tipe_jawaban === 'checkbox') {
                            $pilihanTexts = [];
                            foreach ($answersForThisQuestion as $answer) {
                                $answer->load('pilihanJawaban');
                                if ($answer->pilihanJawaban) {
                                    $pilihanTexts[] = $answer->pilihanJawaban->pilihan;
                                }
                            }
                            $jawabanText = implode('; ', $pilihanTexts);
                        } else {
                            $answer = $answersForThisQuestion->first();
                            if ($answer->jawaban_text) {
                                $jawabanText = $answer->jawaban_text;
                            } elseif ($answer->pilihan_jawaban_id) {
                                $answer->load('pilihanJawaban');
                                $jawabanText = $answer->pilihanJawaban ? $answer->pilihanJawaban->pilihan : 'Pilihan ID tidak valid';
                            }
                        }
                    }

                    // Menulis baris data ke CSV
                    fputcsv($file, [
                        $section->judul,
                        $pertanyaan->pertanyaan,
                        $jawabanText
                    ]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Metode privat untuk membangun query filter agar tidak duplikasi kode.
     */
    private function getFilteredQuery(Request $request)
    {
        $query = StatusPengisian::where('status', 'sudah_diisi')
                                ->with(['user', 'kuesioner']);

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
