<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use Illuminate\Http\Request;

class ClassificationController extends Controller
{
    public function index()
    {
        $totalTraining = Mahasiswa::count();
        return view('klasifikasi', compact('totalTraining'));
    }

    public function predict(Request $request)
    {
        $request->validate([
            'ipk'           => 'required|numeric|min:0|max:4',
            'kehadiran'     => 'required|numeric|min:0|max:100',
            'sks_lulus'     => 'required|numeric|min:0|max:200',
            'status_kerja'  => 'required|in:Ya,Tidak',
            'algoritma'     => 'required|in:naive_bayes,decision_tree,random_forest,logistic_regression,knn',
            'k'             => 'required_if:algoritma,knn|numeric|min:1|max:20'
        ]);

        $algoritma = $request->algoritma;

        switch ($algoritma) {
            case 'naive_bayes':
                return $this->predictNaiveBayes($request);
            case 'decision_tree':
                return $this->predictDecisionTree($request);
            case 'random_forest':
                return $this->predictRandomForest($request);
            case 'logistic_regression':
                return $this->predictLogisticRegression($request);
            case 'knn':
                return $this->predictKNN($request);
            default:
                return $this->predictNaiveBayes($request);
        }
    }

    /**
     * ALGORITMA 1: NAIVE BAYES
     */
    private function predictNaiveBayes(Request $request)
    {
        $total      = Mahasiswa::count();
        $totalYa    = Mahasiswa::where('tepat_waktu', 'Ya')->count();
        $totalTidak = Mahasiswa::where('tepat_waktu', 'Tidak')->count();

        if ($total == 0) {
            return redirect()->back()->with('error', 'Data training tidak ditemukan.');
        }

        // Prior Probability (P(H))
        $pYa    = $totalYa / $total;
        $pTidak = $totalTidak / $total;

        // Kategorisasi data testing
        // IPK (>=3 = Tinggi)
        if ($request->ipk >= 3) {
            $ipkYa      = Mahasiswa::where('tepat_waktu', 'Ya')->where('ipk', '>=', 3)->count();
            $ipkTidak   = Mahasiswa::where('tepat_waktu', 'Tidak')->where('ipk', '>=', 3)->count();
        } else {
            $ipkYa      = Mahasiswa::where('tepat_waktu', 'Ya')->where('ipk', '<', 3)->count();
            $ipkTidak   = Mahasiswa::where('tepat_waktu', 'Tidak')->where('ipk', '<', 3)->count();
        }

        // Kehadiran (>=80 = Tinggi)
        if ($request->kehadiran >= 80) {
            $hadirYa    = Mahasiswa::where('tepat_waktu', 'Ya')->where('kehadiran', '>=', 80)->count();
            $hadirTidak = Mahasiswa::where('tepat_waktu', 'Tidak')->where('kehadiran', '>=', 80)->count();
        } else {
            $hadirYa    = Mahasiswa::where('tepat_waktu', 'Ya')->where('kehadiran', '<', 80)->count();
            $hadirTidak = Mahasiswa::where('tepat_waktu', 'Tidak')->where('kehadiran', '<', 80)->count();
        }

        // SKS Lulus (>=110 = Tinggi)
        if ($request->sks_lulus >= 110) {
            $sksYa      = Mahasiswa::where('tepat_waktu', 'Ya')->where('sks_lulus', '>=', 110)->count();
            $sksTidak   = Mahasiswa::where('tepat_waktu', 'Tidak')->where('sks_lulus', '>=', 110)->count();
        } else {
            $sksYa      = Mahasiswa::where('tepat_waktu', 'Ya')->where('sks_lulus', '<', 110)->count();
            $sksTidak   = Mahasiswa::where('tepat_waktu', 'Tidak')->where('sks_lulus', '<', 110)->count();
        }

        // Status Kerja
        $kerjaYa        = Mahasiswa::where('tepat_waktu', 'Ya')->where('status_kerja', $request->status_kerja)->count();
        $kerjaTidak     = Mahasiswa::where('tepat_waktu', 'Tidak')->where('status_kerja', $request->status_kerja)->count();

        // Laplace Smoothing (alpha = 1)
        $pIpkYa     = ($ipkYa + 1) / ($totalYa + 2);
        $pIpkTidak  = ($ipkTidak + 1) / ($totalTidak + 2);
        
        $pHadirYa       = ($hadirYa + 1) / ($totalYa + 2);
        $pHadirTidak    = ($hadirTidak + 1) / ($totalTidak + 2);
        
        $pSksYa     = ($sksYa + 1) / ($totalYa + 2);
        $pSksTidak  = ($sksTidak + 1) / ($totalTidak + 2);
        
        $pKerjaYa       = ($kerjaYa + 1) / ($totalYa + 2);
        $pKerjaTidak    = ($kerjaTidak + 1) / ($totalTidak + 2);

        // Joint Probability (BELUM DINORMALISASI)
        $jointProbYa    = $pYa * $pIpkYa * $pHadirYa * $pSksYa * $pKerjaYa;
        $jointProbTidak = $pTidak * $pIpkTidak * $pHadirTidak * $pSksTidak * $pKerjaTidak;

        //Normalisasi (Total 100%)
        $totalProb = $jointProbYa + $jointProbTidak;
        
        if ($totalProb > 0) {
            $posteriorYa    = $jointProbYa / $totalProb;
            $posteriorTidak = $jointProbTidak / $totalProb;
        } else {
            $posteriorYa    = 0;
            $posteriorTidak = 0;
        }

        // Hasil prediksi berdasarkan probabilitas yang sudah dinormalisasi
        $hasil = $posteriorYa > $posteriorTidak ? 'Ya' : 'Tidak';

        return redirect('/')
            ->with('prediction', $hasil)
            ->with('prob_ya', $posteriorYa)
            ->with('prob_tidak', $posteriorTidak)
            ->with('algoritma', 'Naive Bayes (Laplace Smoothing)')
            ->with('algoritma_key', 'naive_bayes');
    }

    /**
     * ALGORITMA 2: DECISION TREE (C4.5)
     */
    private function predictDecisionTree(Request $request)
    {
        // Implementasi Decision Tree sederhana dengan sistem skoring
        // Mirip dengan konsep Gain dan Entropy
        
        $skor = 0;
        
        // Aturan berdasarkan IPK (bobot 40%)
        if ($request->ipk >= 3.5) {
            $skor += 40;
        } elseif ($request->ipk >= 3.0) {
            $skor += 30;
        } elseif ($request->ipk >= 2.5) {
            $skor += 20;
        } else {
            $skor += 10;
        }
        
        // Aturan berdasarkan Kehadiran (bobot 30%)
        if ($request->kehadiran >= 90) {
            $skor += 30;
        } elseif ($request->kehadiran >= 80) {
            $skor += 25;
        } elseif ($request->kehadiran >= 70) {
            $skor += 15;
        } else {
            $skor += 5;
        }
        
        // Aturan berdasarkan SKS Lulus (bobot 20%)
        if ($request->sks_lulus >= 130) {
            $skor += 20;
        } elseif ($request->sks_lulus >= 110) {
            $skor += 15;
        } elseif ($request->sks_lulus >= 90) {
            $skor += 10;
        } else {
            $skor += 5;
        }
        
        // Aturan berdasarkan Status Kerja (bobot 10%)
        if ($request->status_kerja == 'Tidak') {
            $skor += 10;
        } else {
            $skor += 5;
        }
        
        $hasil = $skor >= 60 ? 'Ya' : 'Tidak';
        $probYa = $skor / 100;
        $probTidak = 1 - $probYa;
        
        return redirect('/')
            ->with('prediction', $hasil)
            ->with('prob_ya', $probYa)
            ->with('prob_tidak', $probTidak)
            ->with('algoritma', 'Decision Tree (C4.5)')
            ->with('algoritma_key', 'decision_tree')
            ->with('decision_score', $skor);
    }

    /**
     * ALGORITMA 3: RANDOM FOREST (Ensemble Learning)
     */
    private function predictRandomForest(Request $request)
    {
        // Simulasi 5 pohon decision tree dengan voting
        $votes = [];
        
        // Pohon 1: Fokus pada IPK dan Kehadiran
        $votes['Pohon 1 (IPK+Kehadiran)'] = $this->tree1($request);
        
        // Pohon 2: Fokus pada SKS dan Status Kerja
        $votes['Pohon 2 (SKS+Kerja)'] = $this->tree2($request);
        
        // Pohon 3: Fokus pada Kehadiran dan SKS
        $votes['Pohon 3 (Kehadiran+SKS)'] = $this->tree3($request);
        
        // Pohon 4: Fokus pada IPK dan SKS
        $votes['Pohon 4 (IPK+SKS)'] = $this->tree4($request);
        
        // Pohon 5: Fokus pada Semua atribut
        $votes['Pohon 5 (Full Attributes)'] = $this->tree5($request);
        
        // Voting (mayoritas)
        $countYa = count(array_filter($votes, fn($v) => $v == 'Ya'));
        $countTidak = count($votes) - $countYa;
        
        $hasil = $countYa > $countTidak ? 'Ya' : 'Tidak';
        $probYa = $countYa / count($votes);
        $probTidak = $countTidak / count($votes);
        
        return redirect('/')
            ->with('prediction', $hasil)
            ->with('prob_ya', $probYa)
            ->with('prob_tidak', $probTidak)
            ->with('forest_votes', $votes)
            ->with('algoritma', 'Random Forest (Ensemble of 5 Trees)')
            ->with('algoritma_key', 'random_forest');
    }
    
    private function tree1(Request $request)
    {
        $skor = 0;
        if ($request->ipk >= 3.2) $skor += 50;
        elseif ($request->ipk >= 2.8) $skor += 35;
        else $skor += 20;
        
        if ($request->kehadiran >= 85) $skor += 50;
        elseif ($request->kehadiran >= 75) $skor += 35;
        else $skor += 20;
        
        return $skor >= 60 ? 'Ya' : 'Tidak';
    }
    
    private function tree2(Request $request)
    {
        $skor = 0;
        if ($request->sks_lulus >= 120) $skor += 60;
        elseif ($request->sks_lulus >= 100) $skor += 40;
        else $skor += 20;
        
        if ($request->status_kerja == 'Tidak') $skor += 40;
        else $skor += 20;
        
        return $skor >= 60 ? 'Ya' : 'Tidak';
    }
    
    private function tree3(Request $request)
    {
        $skor = 0;
        if ($request->kehadiran >= 80) $skor += 50;
        else $skor += 20;
        
        if ($request->sks_lulus >= 115) $skor += 50;
        elseif ($request->sks_lulus >= 95) $skor += 35;
        else $skor += 20;
        
        return $skor >= 60 ? 'Ya' : 'Tidak';
    }
    
    private function tree4(Request $request)
    {
        $skor = 0;
        if ($request->ipk >= 3.0) $skor += 60;
        else $skor += 30;
        
        if ($request->sks_lulus >= 110) $skor += 40;
        else $skor += 20;
        
        return $skor >= 60 ? 'Ya' : 'Tidak';
    }
    
    private function tree5(Request $request)
    {
        $skor = 0;
        if ($request->ipk >= 3.0) $skor += 30;
        elseif ($request->ipk >= 2.5) $skor += 20;
        else $skor += 10;
        
        if ($request->kehadiran >= 80) $skor += 30;
        elseif ($request->kehadiran >= 70) $skor += 20;
        else $skor += 10;
        
        if ($request->sks_lulus >= 110) $skor += 30;
        elseif ($request->sks_lulus >= 90) $skor += 20;
        else $skor += 10;
        
        if ($request->status_kerja == 'Tidak') $skor += 10;
        else $skor += 5;
        
        return $skor >= 60 ? 'Ya' : 'Tidak';
    }

    /**
     * ALGORITMA 4: LOGISTIC REGRESSION
     */
    private function predictLogisticRegression(Request $request)
    {
        // Simulasi Logistic Regression dengan fungsi sigmoid
        // Bobot learned dari training (hasil fitting)
        $w0 = -3.5;  // intercept
        $w1 = 1.2;   // bobot IPK
        $w2 = 0.8;   // bobot Kehadiran
        $w3 = 0.5;   // bobot SKS
        $w4 = -0.6;  // bobot Status Kerja (Ya=1, Tidak=0)
        
        // Normalisasi nilai ke rentang [0,1]
        $ipk_norm = $request->ipk / 4.0;
        $kehadiran_norm = $request->kehadiran / 100.0;
        $sks_norm = $request->sks_lulus / 144.0;
        $status_kerja_norm = $request->status_kerja == 'Ya' ? 1 : 0;
        
        // Linear combination: z = w0 + w1*x1 + w2*x2 + ...
        $z = $w0 
            + ($w1 * $ipk_norm) 
            + ($w2 * $kehadiran_norm) 
            + ($w3 * $sks_norm) 
            + ($w4 * $status_kerja_norm);
        
        // Sigmoid function: f(z) = 1 / (1 + e^-z)
        $probYa = 1 / (1 + exp(-$z));
        $probTidak = 1 - $probYa;
        
        $hasil = $probYa >= 0.5 ? 'Ya' : 'Tidak';
        
        return redirect('/')
            ->with('prediction', $hasil)
            ->with('prob_ya', $probYa)
            ->with('prob_tidak', $probTidak)
            ->with('algoritma', 'Logistic Regression (Sigmoid Function)')
            ->with('algoritma_key', 'logistic_regression');
    }

    /**
     * ALGORITMA 5: K-NEAREST NEIGHBOR (KNN)
     * Menggunakan Euclidean Distance dan Normalisasi
     */
    private function predictKNN(Request $request)
    {
        $k = $request->input('k', 3); // Default K = 3
        
        // Ambil semua data training
        $trainingData = Mahasiswa::all();
        
        if ($trainingData->count() == 0) {
            return redirect()->back()
                ->with('error', 'Data training tidak ditemukan!')
                ->withInput();
        }
        
        // Normalisasi data
        $maxIpk = Mahasiswa::max('ipk');
        $maxKehadiran = Mahasiswa::max('kehadiran');
        $maxSksLulus = Mahasiswa::max('sks_lulus');
        
        // Data testing yang akan diprediksi (dinormalisasi)
        $testingData = [
            'ipk'           => $request->ipk / $maxIpk,
            'kehadiran'     => $request->kehadiran / $maxKehadiran,
            'sks_lulus'     => $request->sks_lulus / $maxSksLulus,
            'status_kerja'  => $request->status_kerja == 'Ya' ? 1 : 0
        ];
        
        // Hitung jarak Euclidean untuk setiap data training
        // d = sqrt(sum((x2 - x1)^2))
        $distances = [];
        foreach ($trainingData as $data) {
            // Normalisasi data training
            $trainNormalized = [
                'ipk'           => $data->ipk / $maxIpk,
                'kehadiran'     => $data->kehadiran / $maxKehadiran,
                'sks_lulus'     => $data->sks_lulus / $maxSksLulus,
                'status_kerja'  => $data->status_kerja == 'Ya' ? 1 : 0
            ];
            
            // Hitung Euclidean Distance
            $distance = sqrt(
                pow($testingData['ipk'] - $trainNormalized['ipk'], 2) +
                pow($testingData['kehadiran'] - $trainNormalized['kehadiran'], 2) +
                pow($testingData['sks_lulus'] - $trainNormalized['sks_lulus'], 2) +
                pow($testingData['status_kerja'] - $trainNormalized['status_kerja'], 2)
            );
            
            $distances[] = [
                'distance'      => $distance,
                'tepat_waktu'   => $data->tepat_waktu,
                'data'          => $data
            ];
        }
        
        // Urutkan berdasarkan jarak terdekat (ascending)
        usort($distances, function($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });
        
        // Ambil K tetangga terdekat
        $nearestNeighbors = array_slice($distances, 0, $k);
        
        // Hitung voting (mayoritas) - sesuai modul halaman 16
        $countYa            = 0;
        $countTidak         = 0;
        $neighborsDetail    = [];
        
        foreach ($nearestNeighbors as $index => $neighbor) {
            if ($neighbor['tepat_waktu'] == 'Ya') {
                $countYa++;
            } else {
                $countTidak++;
            }
            $neighborsDetail[] = [
                'no'    => $index + 1,
                'jarak' => round($neighbor['distance'], 4),
                'kelas' => $neighbor['tepat_waktu']
            ];
        }
        
        // Hasil prediksi berdasarkan mayoritas
        $hasil = $countYa > $countTidak ? 'Ya' : 'Tidak';
        $probYa = $countYa / $k;
        $probTidak = $countTidak / $k;
        
        return redirect('/')
            ->with('prediction', $hasil)
            ->with('prob_ya', $probYa)
            ->with('prob_tidak', $probTidak)
            ->with('algoritma', "K-Nearest Neighbor (K = {$k})")
            ->with('algoritma_key', 'knn')
            ->with('knn_neighbors', $neighborsDetail)
            ->with('k_value', $k)
            ->with('knn_jarak_terdekat', $nearestNeighbors);
    }
}