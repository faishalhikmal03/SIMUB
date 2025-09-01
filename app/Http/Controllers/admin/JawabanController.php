<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jawaban;
use App\Models\Kuesioner;
use App\Models\Pertanyaan;
use App\Models\StatusPengisian;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class JawabanController extends Controller
{
    //PAGINATION MANAJEMEN JAWABAN
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

    // DETAIL JAWABAN RESPONDEN
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

    // HAPUS JAWABAN
    public function destroy($submissionUuid)
    {
        DB::beginTransaction();
        try {
            $jawabanDihapus = Jawaban::where('submission_uuid', $submissionUuid)->delete();

            if ($jawabanDihapus == 0) {
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

    // EKSPOR CSV LIST RESPONDEN
    public function export(Request $request)
    {
        $hasilPengisian = $this->getFilteredSubmissionsQuery($request)->get();

        $fileName = 'ringkasan_jawaban_kuesioner_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($hasilPengisian) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['ID Sesi', 'Nama Pengisi', 'Role', 'NPM/NIDN/Yudisium', 'Judul Kuesioner', 'Waktu Pengisian']);

            foreach ($hasilPengisian as $hasil) {
                $user = $hasil->user;
                $identifier = '';
                if (in_array($user->role, ['mahasiswa', 'mahasiswa_baru'])) {
                    $identifier = (string) ($user->npm ?? 'N/A');
                } elseif ($user->role == 'dosen') {
                    $identifier = (string) ($user->nidn ?? 'N/A');
                } elseif ($user->role == 'alumni') {
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

    // EKSPOR DETAIL JAWABAN RESPONDEN
    public function exportDetail($submissionUuid)
    {
        $answers = Jawaban::where('submission_uuid', $submissionUuid)
            ->with(['kuesioner', 'user', 'section', 'pertanyaan', 'pilihanJawaban', 'dosen'])
            ->get();

        if ($answers->isEmpty()) {
            abort(404, 'Data jawaban untuk sesi ini tidak ditemukan.');
        }

        $kuesioner = $answers->first()->kuesioner;
        $user = $answers->first()->user;
        $dosenYangDinilai = $answers->first()->dosen;

        // --- Ambil data semua dosen dalam satu query untuk efisiensi ---
        $allDosenIds = $answers->whereNotNull('pilihanJawaban.value')->pluck('pilihanJawaban.value');
        $dosenMap = User::whereIn('id', $allDosenIds)->get()->keyBy('id');

        $fileName = 'jawaban_detail-' . Str::slug($user->nama) . '.csv';
        $headers = ["Content-type" => "text/csv; charset=utf-8", "Content-Disposition" => "attachment; filename=$fileName", /* ... */];

        $callback = function () use ($kuesioner, $user, $dosenYangDinilai, $answers, $dosenMap) {
            $file = fopen('php://output', 'w');

            // Header Informasi
            fputcsv($file, ['sep=,']);
            fputcsv($file, ['Judul Kuesioner:', $kuesioner->judul]);
            fputcsv($file, ['Diisi oleh:', $user->nama]);

            $identifier = '';
            if (in_array($user->role, ['mahasiswa', 'mahasiswa_baru']))
                $identifier = 'NPM: ' . ($user->npm ?? 'N/A');
            elseif ($user->role == 'dosen')
                $identifier = 'NIDN: ' . ($user->nidn ?? 'N/A');
            elseif ($user->role == 'alumni')
                $identifier = 'Tgl Yudisium: ' . ($user->tanggal_yudisium ? \Carbon\Carbon::parse($user->tanggal_yudisium)->format('Y-m-d') : 'N/A');
            fputcsv($file, ['Identitas:', $identifier]);

            if ($dosenYangDinilai) {
                fputcsv($file, ['Menilai Dosen:', $dosenYangDinilai->nama]);
            }
            fputcsv($file, []);
            fputcsv($file, ['Section', 'Pertanyaan', 'Jawaban']);

            // Buat 'peta' jawaban agar pencarian lebih cepat
            $answerMap = $answers->groupBy('pertanyaan_id');
            $kuesioner->load('sections.pertanyaans');

            foreach ($kuesioner->sections as $section) {
                foreach ($section->pertanyaans as $pertanyaan) {
                    $jawabanText = 'TIDAK DIJAWAB';

                    if (isset($answerMap[$pertanyaan->id])) {
                        $jawabanSesiIni = [];
                        foreach ($answerMap[$pertanyaan->id] as $answer) {
                            if ($answer->jawaban_text) {
                                $jawabanSesiIni[] = $answer->jawaban_text;
                            } elseif ($pertanyaan->tipe_jawaban == 'pilihan_dosen' && $answer->pilihanJawaban) {
                                // 1. Ambil ID User Dosen dari kolom 'value'
                                $dosenId = $answer->pilihanJawaban->value;
                                // 2. Cari nama dosen di 'peta' yang sudah kita buat
                                $jawabanSesiIni[] = $dosenMap[$dosenId]->nama ?? 'Error: Dosen ID ' . $dosenId . ' tidak ditemukan';
                            } elseif ($answer->pilihanJawaban) {
                                $jawabanSesiIni[] = $answer->pilihanJawaban->pilihan;
                            }
                        }
                        $jawabanText = implode('; ', $jawabanSesiIni);
                    }

                    fputcsv($file, [$section->judul, $pertanyaan->pertanyaan, $jawabanText]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // HALAMAN REKAPITULASI
    public function rekapDosenIndex(Request $request)
    {
        // Langkah 1: Cari kuesioner yang memiliki data penilaian dosen
        $pertanyaanIdsDenganPenilaian = Jawaban::whereNotNull('dosen_id')->distinct()->pluck('pertanyaan_id');
        $sectionIdsDenganPenilaian = Pertanyaan::whereIn('id', $pertanyaanIdsDenganPenilaian)->distinct()->pluck('section_id');
        $kuesionerIdsDenganPenilaian = DB::table('sections')->whereIn('id', $sectionIdsDenganPenilaian)->distinct()->pluck('kuesioner_id');
        $kuesioners = Kuesioner::whereIn('id', $kuesionerIdsDenganPenilaian)->orderBy('judul')->get();

        $dosens = collect();
        $selectedKuesioner = null;

        if ($request->filled('kuesioner_id')) {
            $selectedKuesioner = Kuesioner::with('sections')->findOrFail($request->kuesioner_id);

            // Logika di bawah ini tidak lagi mencari kuesioner_id di tabel jawabans
            $pertanyaanIdsDiKuesioner = Pertanyaan::whereIn('section_id', $selectedKuesioner->sections->pluck('id'))->pluck('id');

            $evaluatedDosenIds = Jawaban::whereIn('pertanyaan_id', $pertanyaanIdsDiKuesioner)
                ->whereNotNull('dosen_id')
                ->distinct()
                ->pluck('dosen_id');

            $dosens = User::whereIn('id', $evaluatedDosenIds)->orderBy('nama')->get();
        }

        return view('admin.jawaban.rekapitulasi_dosen', compact('kuesioners', 'selectedKuesioner', 'dosens'));
    }

    // EKSPOR REKAPITULASI UNTUK DOSEN
    public function exportRekapitulasiPerDosen(Kuesioner $kuesioner, User $user)
    {
        // Langkah 1A: Dapatkan semua ID section secara langsung dari database.
        $sectionIds = DB::table('sections')->where('kuesioner_id', $kuesioner->id)->pluck('id');

        // Pengaman: Jika kuesioner ini tidak memiliki section di database.
        if ($sectionIds->isEmpty()) {
            return redirect()->back()->with('error', 'Kuesioner ini tidak memiliki section.');
        }

        // Langkah 1B: Dari ID section tersebut, cari semua pertanyaan yang bertipe 'single_option'.
        $pertanyaanPenilaianIds = Pertanyaan::whereIn('section_id', $sectionIds)
            ->where('tipe_jawaban', 'single_option')
            ->pluck('id');

        // Pengaman: Jika tidak ada pertanyaan 'single_option' di kuesioner ini.
        if ($pertanyaanPenilaianIds->isEmpty()) {
            return redirect()->back()->with('error', 'Kuesioner ini tidak memiliki pertanyaan penilaian (single_option).');
        }

        // Langkah 2: Hitung responden unik.
        $totalResponden = Jawaban::where('dosen_id', $user->id)
            ->whereIn('pertanyaan_id', $pertanyaanPenilaianIds)
            ->distinct('user_id')
            ->count('user_id');

        // Langkah 3: Agregasi data jawaban.
        $results = DB::table('jawabans')
            ->join('pilihan_jawabans', 'jawabans.pilihan_jawaban_id', '=', 'pilihan_jawabans.id')
            ->select('jawabans.pertanyaan_id', 'pilihan_jawabans.pilihan as jawaban', DB::raw('count(*) as jumlah'))
            ->where('jawabans.dosen_id', $user->id)
            ->whereIn('jawabans.pertanyaan_id', $pertanyaanPenilaianIds)
            ->groupBy('jawabans.pertanyaan_id', 'pilihan_jawabans.pilihan')
            ->get();

        // Langkah 4: Proses data untuk CSV.
        $rekapData = [];
        $pertanyaans = Pertanyaan::whereIn('id', $pertanyaanPenilaianIds)->get()->keyBy('id');
        foreach ($pertanyaans as $id => $pertanyaan) {
            $rekapData[$id] = ['pertanyaan' => $pertanyaan->pertanyaan, 'respon' => ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0]];
        }
        foreach ($results as $result) {
            if (isset($rekapData[$result->pertanyaan_id]) && isset($rekapData[$result->pertanyaan_id]['respon'][$result->jawaban])) {
                $rekapData[$result->pertanyaan_id]['respon'][$result->jawaban] = $result->jumlah;
            }
        }

        // Langkah 5: Generate & Download file CSV (Lengkap).
        $fileName = 'rekap-' . Str::slug($user->nama) . '-' . Str::slug($kuesioner->judul) . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($kuesioner, $user, $totalResponden, $rekapData) {
            $file = fopen('php://output', 'w');

            // Header Informasi File
            fputcsv($file, ['REKAPITULASI HASIL KUESIONER']);
            fputcsv($file, []);
            fputcsv($file, ['Nama Dosen:', $user->nama]);
            fputcsv($file, ['NIDN:', (string) ($user->nidn ?? 'N/A')]);
            fputcsv($file, ['Judul Kuesioner:', $kuesioner->judul]);
            fputcsv($file, ['Total Responden:', $totalResponden]);
            fputcsv($file, []);

            // Header Tabel
            fputcsv($file, ['No', 'Pertanyaan', 'Respon 1', 'Respon 2', 'Respon 3', 'Respon 4', 'Respon 5']);

            $nomor = 1;
            foreach ($rekapData as $data) {
                fputcsv($file, [
                    $nomor++,
                    $data['pertanyaan'],
                    $data['respon']['1'],
                    $data['respon']['2'],
                    $data['respon']['3'],
                    $data['respon']['4'],
                    $data['respon']['5'],
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // METODE PRIVAT QUERY FILTER AGAR TIDAK ADA DUPLIKASI
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

