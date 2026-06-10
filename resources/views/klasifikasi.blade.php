<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Prediksi Kelulusan Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .btn-predict {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-predict:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .algoritma-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .stat-card {
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: default;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .icon-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px auto;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-11">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white text-center py-4 position-relative">
                        <div class="algoritma-badge">
                            <i class="fas fa-microchip"></i> 5 Algoritma Klasifikasi
                        </div>
                        <h2 class="mb-2">
                            <i class="fas fa-graduation-cap"></i> 
                            Sistem Prediksi Kelulusan Mahasiswa
                        </h2>
                        <p class="mb-0">
                            <i class="fas fa-robot"></i> Implementasi Machine Learning: 
                            Naive Bayes | C4.5 | Random Forest | Logistic Regression | KNN
                        </p>
                        <small>
                            <i class="fas fa-user"></i> SUKUR - 411231087 <i class="fas fa-book-open"></i> Tugas Pertemuan 11 - Analitik dan Virtualisasi Data
                        </small>
                    </div>

                    <div class="card-body p-4">
                        <!-- Stats Cards -->
                        <div class="row mb-4 g-3">
                            <div class="col-md-3">
                                <div class="card border-success h-100 stat-card">
                                    <div class="card-body text-center">
                                        <div class="icon-circle bg-success bg-opacity-10">
                                            <i class="fas fa-database fa-2x text-success"></i>
                                        </div>
                                        <h6 class="text-success mt-2">
                                            <i class="fas fa-chart-line"></i> Data Training
                                        </h6>
                                        <h3 class="fw-bold">{{ $totalTraining ?? 0 }}</h3>
                                        <small>Data Historis Mahasiswa</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info h-100 stat-card">
                                    <div class="card-body text-center">
                                        <div class="icon-circle bg-info bg-opacity-10">
                                            <i class="fas fa-brain fa-2x text-info"></i>
                                        </div>
                                        <h6 class="text-info mt-2">
                                            <i class="fas fa-microchip"></i> Algoritma Terakhir
                                        </h6>
                                        <h5 class="fw-bold" style="font-size: 14px;">{{ session('algoritma') ?? 'Belum ada' }}</h5>
                                        <small>Classification Method</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning h-100 stat-card">
                                    <div class="card-body text-center">
                                        <div class="icon-circle bg-warning bg-opacity-10">
                                            <i class="fas fa-code-branch fa-2x text-warning"></i>
                                        </div>
                                        <h6 class="text-warning mt-2">
                                            <i class="fas fa-cogs"></i> Metode
                                        </h6>
                                        <h5 class="fw-bold">5 Algoritma</h5>
                                        <small>Naïve Bayes, C4.5, RF, LR, KNN</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-danger h-100 stat-card">
                                    <div class="card-body text-center">
                                        <div class="icon-circle bg-danger bg-opacity-10">
                                            <i class="fas fa-chart-simple fa-2x text-danger"></i>
                                        </div>
                                        <h6 class="text-danger mt-2">
                                            <i class="fas fa-bullseye"></i> Akurasi
                                        </h6>
                                        <h3 class="fw-bold">~85%</h3>
                                        <small>Based on 500 data</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h4 class="mb-3">
                            <i class="fas fa-pen-alt text-primary"></i> Form Data Testing
                        </h4>

                        <form action="{{ url('/predict') }}" method="POST" id="predictionForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-chart-pie"></i> Pilih Algoritma Klasifikasi
                                    </label>
                                    <select class="form-select" name="algoritma" id="algoritmaSelect" required>
                                        <option value="naive_bayes" {{ session('algoritma_key') == 'naive_bayes' ? 'selected' : '' }}>
                                            <i class="fas fa-chart-bar"></i> 1. Naive Bayes (Probabilitas & Laplace Smoothing)
                                        </option>
                                        <option value="decision_tree" {{ session('algoritma_key') == 'decision_tree' ? 'selected' : '' }}>
                                            <i class="fas fa-tree"></i> 2. Decision Tree C4.5 (Pohon Keputusan dengan Skoring)
                                        </option>
                                        <option value="random_forest" {{ session('algoritma_key') == 'random_forest' ? 'selected' : '' }}>
                                            <i class="fas fa-forest"></i> 3. Random Forest (Ensemble 5 Pohon + Voting)
                                        </option>
                                        <option value="logistic_regression" {{ session('algoritma_key') == 'logistic_regression' ? 'selected' : '' }}>
                                            <i class="fas fa-chart-line"></i> 4. Logistic Regression (Sigmoid Function)
                                        </option>
                                        <option value="knn" {{ session('algoritma_key') == 'knn' ? 'selected' : '' }}>
                                            <i class="fas fa-users"></i> 5. K-Nearest Neighbor KNN (Euclidean Distance)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Input khusus KNN -->
                            <div class="row" id="knnOption" style="display: none;">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-sort-numeric-up"></i> Nilai K (Jumlah Tetangga Terdekat)
                                    </label>
                                    <input type="number" name="k" class="form-control" value="{{ session('k_value', 3) }}" min="1" max="20" step="1">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> K = 3 adalah nilai default
                                    </small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-ruler-combined"></i> Metode Jarak
                                    </label>
                                    <input type="text" class="form-control" value="Euclidean Distance" disabled>
                                    <small class="text-muted">
                                        <i class="fas fa-square-root-alt"></i> d = √(Σ(x₂ - x₁)²)
                                    </small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-star text-warning"></i> IPK
                                    </label>
                                    <input type="number" step="0.01" min="0" max="4" name="ipk" class="form-control" placeholder="Contoh: 3.50" value="{{ old('ipk') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-calendar-check"></i> Kehadiran (%)
                                    </label>
                                    <input type="number" min="0" max="100" name="kehadiran" class="form-control" placeholder="Contoh: 90" value="{{ old('kehadiran') }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-book"></i> SKS Lulus
                                    </label>
                                    <input type="number" name="sks_lulus" class="form-control" placeholder="Contoh: 120" value="{{ old('sks_lulus') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-briefcase"></i> Status Kerja
                                    </label>
                                    <select class="form-select" name="status_kerja" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Ya" {{ old('status_kerja') == 'Ya' ? 'selected' : '' }}>
                                            <i class="fas fa-check-circle text-success"></i> Ya (Bekerja)
                                        </option>
                                        <option value="Tidak" {{ old('status_kerja') == 'Tidak' ? 'selected' : '' }}>
                                            <i class="fas fa-times-circle text-danger"></i> Tidak (Tidak Bekerja)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-success btn-lg btn-predict">
                                    <i class="fas fa-chart-line"></i> Prediksi Kelulusan
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Informasi Algoritma -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold">
                                <i class="fas fa-microchip text-primary"></i> 5 Algoritma Klasifikasi:
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small>
                                        <i class="fas fa-chart-bar text-success"></i> <strong>Naive Bayes</strong> : Probabilitas & Laplace Smoothing<br>
                                        <i class="fas fa-tree text-success"></i> <strong>Decision Tree C4.5</strong> : Gain & Entropy<br>
                                        <i class="fas fa-forest text-success"></i> <strong>Random Forest</strong> : Ensemble Learning (5 Pohon + Voting)
                                    </small>
                                </div>
                                <div class="col-md-6">
                                    <small>
                                        <i class="fas fa-chart-line text-success"></i> <strong>Logistic Regression</strong> : Fungsi Sigmoid<br>
                                        <i class="fas fa-users text-success"></i> <strong>K-Nearest Neighbor</strong> : Euclidean Distance & Normalisasi<br>
                                        <i class="fas fa-chart-simple text-success"></i> <strong>Akurasi Sistem</strong> : ±85% dari 500 Data Training
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-center text-muted py-3">
                        <i class="fas fa-copyright"></i> 2026 Data Mining - Klasifikasi Kelulusan Mahasiswa
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const algoritmaSelect = document.getElementById('algoritmaSelect');
        const knnOption = document.getElementById('knnOption');
        
        function toggleKnnOption() {
            if (algoritmaSelect.value === 'knn') {
                knnOption.style.display = 'flex';
            } else {
                knnOption.style.display = 'none';
            }
        }
        
        algoritmaSelect.addEventListener('change', toggleKnnOption);
        toggleKnnOption();
    </script>

    @if(session('prediction'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let detailHtml = '';
            let algoritmaKey = '{{ session("algoritma_key") }}';
            
            // Detail untuk Random Forest
            @if(session('algoritma_key') == 'random_forest' && session('forest_votes'))
                detailHtml = `
                    <div class="mt-3 p-3 bg-light border rounded">
                        <p class="mb-2 fw-bold text-dark">
                            <i class="fas fa-tree text-success"></i> Detail Voting Random Forest:
                        </p>
                        <ul class="mb-0" style="list-style-type: none; padding-left: 0;">
                            @foreach(session('forest_votes') as $namaPohon => $vote)
                                <li class="mb-2">
                                    <i class="fas fa-leaf text-success"></i> <strong>{{ $namaPohon }}</strong><br>
                                    <i class="fas fa-gavel"></i> Keputusan: 
                                    <span class="badge {{ $vote == 'Ya' ? 'bg-success' : 'bg-danger' }} px-3 py-1">
                                        <i class="fas {{ $vote == 'Ya' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                        {{ $vote == 'Ya' ? 'Lulus Tepat Waktu' : 'Tidak Lulus' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <hr>
                        <p class="mb-0 text-center">
                            <i class="fas fa-vote-yea"></i> <strong>Hasil Voting:</strong> 
                            {{ count(array_filter(session('forest_votes'), fn($v) => $v == 'Ya')) }} Ya vs 
                            {{ count(array_filter(session('forest_votes'), fn($v) => $v == 'Tidak')) }} Tidak
                        </p>
                    </div>
                `;
            @endif
            
            // Detail untuk KNN
            @if(session('algoritma_key') == 'knn' && session('knn_neighbors'))
                detailHtml = `
                    <div class="mt-3 p-3 bg-light border rounded">
                        <p class="mb-2 fw-bold text-dark">
                            <i class="fas fa-users text-primary"></i> Detail Perhitungan KNN (K = {{ session('k_value') }}):
                        </p>
                        <p class="mb-2 text-muted small">
                            <i class="fas fa-ruler-combined"></i> Metode Jarak: Euclidean Distance 
                            | <i class="fas fa-chart-line"></i> Data telah dinormalisasi
                        </p>
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th><i class="fas fa-hashtag"></i> No</th>
                                    <th><i class="fas fa-arrows-up-down"></i> Jarak Euclidean</th>
                                    <th><i class="fas fa-tag"></i> Kelas Tetangga</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(session('knn_neighbors') as $neighbor)
                                <tr>
                                    <td>{{ $neighbor['no'] }}</td>
                                    <td>{{ $neighbor['jarak'] }}</td>
                                    <td>
                                        <span class="badge {{ $neighbor['kelas'] == 'Ya' ? 'bg-success' : 'bg-danger' }}">
                                            <i class="fas {{ $neighbor['kelas'] == 'Ya' ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            {{ $neighbor['kelas'] == 'Ya' ? 'Lulus Tepat Waktu' : 'Tidak Lulus' }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <p class="mb-0 text-center">
                            <i class="fas fa-chart-simple"></i> <strong>Hasil Voting (Mayoritas):</strong> 
                            {{ session('prob_ya') * session('k_value') }} suara Ya vs 
                            {{ session('prob_tidak') * session('k_value') }} suara Tidak
                        </p>
                        <small class="text-muted d-block text-center mt-2">
                            <i class="fas fa-book-open"></i> Prediksi berdasarkan kategori mayoritas tetangga terdekat
                        </small>
                    </div>
                `;
            @endif
            
            // Detail untuk Decision Tree
            @if(session('algoritma_key') == 'decision_tree' && session('decision_score'))
                detailHtml = `
                    <div class="mt-3 p-3 bg-light border rounded">
                        <p class="mb-2 fw-bold text-dark">
                            <i class="fas fa-tree text-warning"></i> Detail Perhitungan Decision Tree:
                        </p>
                        <p><i class="fas fa-chart-simple"></i> Skor Akhir: <strong>{{ session('decision_score') }}</strong> / 100</p>
                        <div class="progress mb-2" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ session('decision_score') }}%">
                                <i class="fas fa-chart-line"></i> {{ session('decision_score') }}%
                            </div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-gavel"></i> Rule: Lulus jika skor ≥ 60 (sesuai modul C4.5)
                        </small>
                    </div>
                `;
            @endif

            // Tentukan icon dan title berdasarkan hasil prediksi
            const isLulus = '{{ session("prediction") }}' === 'Ya';
            const icon = isLulus ? 'success' : 'warning';
            const titleIcon = isLulus ? '<i class="fas fa-trophy text-success"></i>' : '<i class="fas fa-frown text-warning"></i>';
            const titleText = isLulus ? 'Selamat!' : 'Maaf';
            const resultIcon = isLulus ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>';
            const resultText = isLulus ? 'LULUS TEPAT WAKTU' : 'TIDAK LULUS TEPAT WAKTU';
            const resultColor = isLulus ? '#198754' : '#dc3545';

            Swal.fire({
                icon: icon,
                title: titleIcon + ' ' + titleText,
                html: `
                    <div style="text-align:left; font-size:15px;">
                        <p><i class="fas fa-robot text-primary"></i> <strong>Metode :</strong> {{ session('algoritma') }}</p>
                        <hr>
                        <p><i class="fas fa-chart-line"></i> <strong>Hasil Prediksi Kelulusan :</strong></p>
                        <h3 style="color: ${resultColor}; font-weight: bold;">
                            ${resultIcon} ${resultText}
                        </h3>
                        <hr>
                        <div class="progress mb-2" style="height: 25px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ round(session('prob_ya', 0) * 100, 2) }}%">
                                <i class="fas fa-check"></i> Ya: {{ round(session('prob_ya', 0) * 100, 2) }}%
                            </div>
                            <div class="progress-bar bg-danger" role="progressbar" 
                                 style="width: {{ round(session('prob_tidak', 0) * 100, 2) }}%">
                                <i class="fas fa-times"></i> Tidak: {{ round(session('prob_tidak', 0) * 100, 2) }}%
                            </div>
                        </div>
                        <p class="mb-1">
                            <i class="fas fa-chart-line text-success"></i> 
                            Probabilitas <strong class="text-success">LULUS</strong> : 
                            <strong>{{ round(session('prob_ya', 0) * 100, 2) }}%</strong>
                        </p>
                        <p class="mb-1">
                            <i class="fas fa-chart-line text-danger"></i> 
                            Probabilitas <strong class="text-danger">TIDAK LULUS</strong> : 
                            <strong>{{ round(session('prob_tidak', 0) * 100, 2) }}%</strong>
                        </p>
                        
                        ${detailHtml}
                    </div>
                `,
                width: 750,
                confirmButtonText: '<i class="fas fa-check"></i> Tutup',
                confirmButtonColor: resultColor,
                allowOutsideClick: false
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        Swal.fire({
            icon: 'error',
            title: '<i class="fas fa-exclamation-triangle"></i> Error!',
            html: '<i class="fas fa-database"></i> {{ session("error") }}',
            confirmButtonText: '<i class="fas fa-check"></i> OK'
        });
    </script>
    @endif
</body>
</html>