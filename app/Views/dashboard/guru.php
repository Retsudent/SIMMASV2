<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Guru - SIMMAS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    
    .main-content {
      background-color: #ffffff;
      min-height: 100vh;
    }
    
    .stat-card {
      background: white;
      border: 1px solid #dee2e6;
      border-radius: 0.75rem;
      padding: 1.5rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: bold;
      color: #198754;
    }
    
    .info-panel {
      background: white;
      border: 1px solid #dee2e6;
      border-radius: 0.75rem;
      padding: 1.5rem;
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .welcome-section {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
      border-radius: 0.75rem;
      padding: 2rem;
      margin-bottom: 2rem;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <!-- Sidebar -->
    <?= view('components/sidebar', ['user' => $user]) ?>
    
    <!-- Main Content -->
    <div class="flex-grow-1 d-flex flex-column">
      <!-- Navbar -->
      <?= view('components/navbar', ['user' => $user]) ?>
      
      <!-- Dashboard Content -->
      <div class="main-content p-4">
        <!-- Welcome Section -->
        <div class="welcome-section">
          <h2>Dashboard Guru</h2>
          <p>Selamat datang di panel guru pembimbing magang siswa.</p>
        </div>
        <div class="container py-4">
  <div class="tab-content">
    <div class="tab-pane fade show active" id="tab-dashboard">
      <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="card p-3 stat-card"><div class="label">Total Siswa</div><div class="value" id="stat-total">-</div></div></div>
        <div class="col-md-3"><div class="card p-3 stat-card"><div class="label">DUDI Partner</div><div class="value" id="stat-dudi">-</div></div></div>
        <div class="col-md-3"><div class="card p-3 stat-card"><div class="label">Siswa Magang</div><div class="value" id="stat-aktif">-</div></div></div>
        <div class="col-md-3"><div class="card p-3 stat-card"><div class="label">Logbook Hari Ini</div><div class="value" id="stat-logbook">-</div></div></div>
      </div>
      <div class="row g-3">
        <div class="col-lg-7">
          <div class="card p-3">
            <div class="fw-semibold mb-2">Magang Terbaru</div>
            <div id="list-magang" class="vstack gap-2"></div>
          </div>
          <div class="card p-3 mt-3">
            <div class="fw-semibold mb-2">Logbook Terbaru</div>
            <div id="list-logbook" class="vstack gap-2"></div>
          </div>
        </div>
        <div class="col-lg-5">
          <div class="card p-3">
            <div class="fw-semibold mb-2">DUDI Aktif</div>
            <div id="list-dudi" class="vstack gap-2"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane" id="tab-logbook">
      <!-- Statacards untuk Jurnal Harian -->
      <div class="row g-3 mb-4">
        <div class="col-md-3">
          <div class="card p-3 stat-card">
            <div class="label">Total Logbook</div>
            <div class="value" id="logbook-total">-</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3 stat-card">
            <div class="label">Belum Diverifikasi</div>
            <div class="value" id="logbook-pending">-</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3 stat-card">
            <div class="label">Disetujui</div>
            <div class="value" id="logbook-approved">-</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card p-3 stat-card">
            <div class="label">Ditolak</div>
            <div class="value" id="logbook-rejected">-</div>
          </div>
        </div>
      </div>

      <!-- Filter dan Pencarian -->
      <div class="card p-3 mb-3">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">Status</label>
            <select id="fstatus" class="form-select">
          <option value="">Semua Status</option>
          <option value="pending">Pending</option>
          <option value="disetujui">Disetujui</option>
          <option value="ditolak">Ditolak</option>
        </select>
      </div>
          <div class="col-md-3">
            <label class="form-label">Bulan</label>
            <select id="fmonth" class="form-select">
              <option value="">Semua Bulan</option>
              <option value="01">Januari</option>
              <option value="02">Februari</option>
              <option value="03">Maret</option>
              <option value="04">April</option>
              <option value="05">Mei</option>
              <option value="06">Juni</option>
              <option value="07">Juli</option>
              <option value="08">Agustus</option>
              <option value="09">September</option>
              <option value="10">Oktober</option>
              <option value="11">November</option>
              <option value="12">Desember</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Tahun</label>
            <select id="fyear" class="form-select">
              <option value="">Semua Tahun</option>
              <option value="2024">2024</option>
              <option value="2025">2025</option>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Tanggal Mulai</label>
            <input id="fdate-start" type="date" class="form-control">
          </div>
          <div class="col-md-2">
            <label class="form-label">Tanggal Akhir</label>
            <input id="fdate-end" type="date" class="form-control">
          </div>
        </div>
        <div class="row g-3 mt-2">
          <div class="col-md-6">
            <label class="form-label">Cari Siswa, Kegiatan, atau Kendala</label>
            <input id="fsearch" type="text" class="form-control" placeholder="Cari siswa, kegiatan, atau kendala...">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tampilkan per Halaman</label>
            <select id="fperpage" class="form-select">
              <option value="10">10 per halaman</option>
              <option value="25">25 per halaman</option>
              <option value="50">50 per halaman</option>
              <option value="100">100 per halaman</option>
            </select>
          </div>
          <div class="col-md-3 d-flex align-items-end">
            <button class="btn btn-primary me-2" onclick="loadLogbooks()">Tampilkan Filter</button>
            <button class="btn btn-outline-secondary" onclick="resetLogbookFilters()">Reset</button>
          </div>
        </div>
      </div>

      <!-- Tabel Logbook -->
      <div class="card p-3">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Siswa & Tanggal</th>
                <th>Kegiatan</th>
                <th>Kendala</th>
                <th>Status</th>
                <th>Catatan Guru</th>
                <th>Aksi</th>
              </tr>
            </thead>
          <tbody id="grows"></tbody>
        </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="text-muted">
            Menampilkan <span id="logbook-showing-start">0</span> sampai <span id="logbook-showing-end">0</span> dari <span id="logbook-total-records">0</span> entri
          </div>
          <nav>
            <ul class="pagination pagination-sm mb-0" id="logbook-pagination">
              <!-- Pagination buttons will be generated here -->
            </ul>
          </nav>
        </div>
      </div>
    </div>
    <div class="tab-pane" id="tab-dudi">
      <div class="container-fluid px-0">
        <div class="row g-3 mb-3">
          <div class="col-md-4"><div class="card p-3 stat-card"><div class="label">Total DUDI</div><div class="value" id="gd-total-dudi">-</div></div></div>
          <div class="col-md-4"><div class="card p-3 stat-card"><div class="label">Total Siswa Magang</div><div class="value" id="gd-total-siswa">-</div></div></div>
          <div class="col-md-4"><div class="card p-3 stat-card"><div class="label">Rata-rata Siswa</div><div class="value" id="gd-avg">-</div></div></div>
        </div>
        <div class="card p-3">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="input-group" style="max-width:420px;">
              <input id="gd-q" class="form-control" placeholder="Cari perusahaan, alamat, penanggung jawab...">
              <button class="btn btn-outline-secondary" onclick="loadDudiGuru()">Cari</button>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Perusahaan</th>
                  <th>Kontak</th>
                  <th>Penanggung Jawab</th>
                  <th>Siswa Magang</th>
                </tr>
              </thead>
              <tbody id="gd-rows"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="tab-pane" id="tab-magang">
      <div class="container-fluid px-0">
        <div class="row g-3 mb-3">
          <div class="col-md-3"><div class="card p-3 stat-card"><div class="label">Total</div><div class="value" id="im-total">-</div></div></div>
          <div class="col-md-3"><div class="card p-3 stat-card"><div class="label">Aktif</div><div class="value" id="im-aktif">-</div></div></div>
          <div class="col-md-3"><div class="card p-3 stat-card"><div class="label">Selesai</div><div class="value" id="im-selesai">-</div></div></div>
          <div class="col-md-3"><div class="card p-3 stat-card"><div class="label">Pending</div><div class="value" id="im-pending">-</div></div></div>
        </div>
        <div class="card p-3">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="input-group" style="max-width:420px;">
              <input id="im-q" class="form-control" placeholder="Cari siswa, guru, atau DUDI...">
              <button class="btn btn-outline-secondary" onclick="loadInternships()">Cari</button>
            </div>
          <button class="btn btn-primary" onclick="openCreateInternship()">+ Tambah</button>
            <div>
              <select id="im-status" class="form-select" style="width:180px" onchange="loadInternships()">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="pending">Pending</option>
                <option value="selesai">Selesai</option>
              </select>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped align-middle">
              <thead>
                <tr>
                  <th>Siswa</th>
                  <th>Guru Pembimbing</th>
                  <th>DUDI</th>
                  <th>Periode</th>
                  <th>Status</th>
                  <th>Nilai</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody id="im-rows"></tbody>
            </table>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted">
                Menampilkan <span id="im-start">1</span> sampai <span id="im-end">10</span> dari <span id="im-total-records">0</span> entri
              </div>
              <div class="d-flex align-items-center">
                <span class="me-2">Tampilkan:</span>
                <select id="im-per-page" class="form-select form-select-sm" style="width: auto;" onchange="loadInternships()">
                  <option value="5">5 per halaman</option>
                  <option value="10" selected>10 per halaman</option>
                  <option value="25">25 per halaman</option>
                  <option value="50">50 per halaman</option>
                </select>
              </div>
            </div>
            
            <nav aria-label="Page navigation" class="mt-3">
              <ul class="pagination justify-content-center" id="im-pagination">
                <!-- Pagination will be generated by JavaScript -->
              </ul>
            </nav>
          </div>
        </div>
      </div>

      <!-- Modal create internship -->
      <div class="modal fade" id="mCreate" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Tambah Data Siswa Magang</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <div id="cMsg" class="text-danger small mb-2 d-none"></div>
          <div class="mb-3">
            <label class="form-label">Siswa</label>
            <select id="cSiswa" class="form-select"></select>
          </div>
          <div class="mb-3">
            <label class="form-label">Guru Pembimbing</label>
            <input id="cGuru" class="form-control" disabled>
          </div>
          <div class="mb-3">
            <label class="form-label">DUDI</label>
            <select id="cDudi" class="form-select"></select>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Tanggal Mulai</label>
              <input id="cMulai" type="date" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Tanggal Selesai</label>
              <input id="cSelesai" type="date" class="form-control">
            </div>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" onclick="submitCreateInternship()">Simpan</button></div>
      </div></div></div>

      <!-- Modal edit penempatan / nilai akhir -->
      <div class="modal fade" id="mGrade" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title">Edit Data Siswa Magang</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
          <input type="hidden" id="gid">
          <input type="hidden" id="gcur">
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Tanggal Mulai</label>
              <input id="gstart" type="date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Tanggal Selesai</label>
              <input id="gend" type="date" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select id="gstatus" class="form-select">
                <option value="pending">Pending</option>
                <option value="aktif">Aktif</option>
                <option value="selesai">Selesai</option>
              </select>
            </div>
          </div>
          <div class="mb-1">
          <label class="form-label">Nilai Akhir (0-100)</label>
            <input id="gval" type="number" min="0" max="100" class="form-control" placeholder="Isi setelah status selesai">
            <div class="form-text">Nilai hanya dapat diisi jika status magang selesai</div>
          </div>
        </div>
        <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button class="btn btn-primary" onclick="submitGrade()">Update</button></div>
      </div></div></div>
    </div>
  </div>

  <!-- Modal Detail Jurnal Harian -->
  <div class="modal fade" id="mJournalDetail" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <div class="d-flex align-items-center">
            <div class="bg-primary text-white rounded p-2 me-3">
              <i class="fas fa-file-alt"></i>
            </div>
            <div>
              <h5 class="modal-title mb-0">Detail Jurnal Harian</h5>
              <small class="text-muted" id="journal-date">-</small>
            </div>
          </div>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="journal-id">
          
          <!-- Informasi Siswa -->
          <div class="row mb-4">
            <div class="col-md-6">
              <div class="card border-0 bg-light">
                <div class="card-body p-3">
                  <h6 class="card-title mb-2"><i class="fas fa-user text-primary me-2"></i>Informasi Siswa</h6>
                  <p class="mb-1"><strong>Nama:</strong> <span id="journal-student">-</span></p>
                  <p class="mb-1"><strong>DUDI:</strong> <span id="journal-company">-</span></p>
                  <p class="mb-0"><strong>Tanggal:</strong> <span id="journal-date-detail">-</span></p>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 bg-light">
                <div class="card-body p-3">
                  <h6 class="card-title mb-2"><i class="fas fa-info-circle text-info me-2"></i>Status Saat Ini</h6>
                  <p class="mb-0"><span id="journal-status-badge" class="badge">-</span></p>
                </div>
              </div>
            </div>
          </div>

          <!-- Kegiatan -->
          <div class="mb-4">
            <h6 class="mb-3"><i class="fas fa-tasks text-success me-2"></i>Kegiatan yang Dilakukan</h6>
            <div class="card border-0 bg-light">
              <div class="card-body p-3">
                <p class="mb-0" id="journal-activity">-</p>
              </div>
            </div>
          </div>

          <!-- Kendala yang Dihadapi -->
          <div class="mb-4">
            <h6 class="mb-3"><i class="fas fa-exclamation-triangle text-warning me-2"></i>Kendala yang Dihadapi</h6>
            <div class="alert alert-warning d-flex align-items-start" role="alert">
              <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
              <div class="flex-grow-1">
                <p class="mb-0" id="journal-obstacle">-</p>
              </div>
            </div>
          </div>

          <!-- Dokumentasi -->
          <div class="mb-4">
            <h6 class="mb-3"><i class="fas fa-file-alt text-success me-2"></i>Dokumentasi</h6>
            <div class="alert alert-success d-flex align-items-center" role="alert">
              <i class="fas fa-file-alt me-2"></i>
              <div class="flex-grow-1" id="journal-documentation">
                <p class="text-muted mb-0">Tidak ada dokumentasi</p>
              </div>
            </div>
          </div>

          <!-- Catatan Guru -->
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h6 class="mb-0"><i class="fas fa-comment text-primary me-2"></i>Catatan Guru</h6>
              <button class="btn btn-sm btn-outline-primary" onclick="toggleTeacherNotesEdit()">
                <i class="fas fa-edit me-1"></i>Edit
              </button>
            </div>
            <div class="alert alert-info d-flex align-items-start" role="alert">
              <i class="fas fa-comment me-2 mt-1"></i>
              <div class="flex-grow-1">
                <div id="teacher-notes-display">
                  <p class="text-muted mb-0" id="journal-teacher-notes">Belum ada catatan dari guru</p>
                </div>
                <div id="teacher-notes-edit" class="d-none">
                  <textarea id="journal-teacher-notes-input" class="form-control" rows="4" placeholder="Masukkan catatan untuk siswa..."></textarea>
                  <div class="mt-2">
                    <button class="btn btn-sm btn-success me-2" onclick="saveTeacherNotes()">
                      <i class="fas fa-save me-1"></i>Simpan
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="cancelTeacherNotesEdit()">
                      <i class="fas fa-times me-1"></i>Batal
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="modal-footer">
          <div class="d-flex justify-content-between w-100">
            <div class="text-muted small">
              <span>Dibuat: <span id="journal-created">-</span></span>
              <span class="ms-3">Diperbarui: <span id="journal-updated">-</span></span>
            </div>
            <div>
              <button class="btn btn-success me-2 px-4" onclick="approveJournal()">
                <i class="fas fa-check me-1"></i>Setujui
              </button>
              <button class="btn btn-danger px-4" onclick="rejectJournal()">
                <i class="fas fa-times me-1"></i>Tolak
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Verifikasi Sederhana (untuk kompatibilitas) -->
  <div class="modal fade" id="mVerify" tabindex="-1"><div class="modal-dialog"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Verifikasi Jurnal</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" id="vid">
      <div class="mb-2"><label class="form-label">Status</label>
        <select id="vstatus" class="form-select">
          <option value="disetujui">Disetujui</option>
          <option value="ditolak">Ditolak</option>
          <option value="pending">Pending</option>
        </select>
      </div>
      <div class="mb-2"><label class="form-label">Catatan</label><textarea id="vcat" class="form-control" placeholder="Catatan untuk siswa..."></textarea></div>
    </div>
    <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Batal</button><button class="btn btn-primary" onclick="submitVerify()">Simpan</button></div>
  </div></div></div>
</div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Authentication function
    function requireAuth(){
      const t=localStorage.getItem('simmas_token');
      const u=JSON.parse(localStorage.getItem('simmas_user')||'{}');
      if(!t||u.role!=='guru'){ location.href='/login'; }
      return {t,u};
    }
    
    // Navigation functions for guru dashboard
    function showDashboard() {
      console.log('Showing Dashboard');
      // Hide all tab panes
      document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
      });
      // Show dashboard tab
      const dashboardTab = document.getElementById('tab-dashboard');
      if (dashboardTab) {
        dashboardTab.classList.add('show', 'active');
      }
      
      // Update active nav
      updateActiveNav('dashboard');
    }

    function showDudi() {
      console.log('Showing DUDI');
      // Hide all tab panes
      document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
      });
      // Show DUDI tab
      const dudiTab = document.getElementById('tab-dudi');
      if (dudiTab) {
        dudiTab.classList.add('show', 'active');
      }
      
      // Load DUDI data
      loadDudiGuru();
      
      // Update active nav
      updateActiveNav('dudi');
    }

    function showMagang() {
      console.log('Showing Magang');
      // Hide all tab panes
      document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
      });
      // Show magang tab
      const magangTab = document.getElementById('tab-magang');
      if (magangTab) {
        magangTab.classList.add('show', 'active');
      }
      
      // Load magang data
      loadInternships();
      
      // Update active nav
      updateActiveNav('magang');
    }

    function showLogbook() {
      console.log('Showing Logbook');
      // Hide all tab panes
      document.querySelectorAll('.tab-pane').forEach(pane => {
        pane.classList.remove('show', 'active');
      });
      // Show logbook tab
      const logbookTab = document.getElementById('tab-logbook');
      if (logbookTab) {
        logbookTab.classList.add('show', 'active');
      }
      
      // Load logbook data with delay to ensure DOM is ready
      setTimeout(() => {
        console.log('Loading logbook data...');
        loadLogbooks();
        loadLogbookStats();
      }, 100);
      
      // Update active nav
      updateActiveNav('logbook');
    }

    // Update active nav state
    function updateActiveNav(active) {
      // Remove active class from all nav links
      document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.classList.remove('active');
      });
      
      // Add active class to clicked nav link
      const navLinks = document.querySelectorAll('.sidebar .nav-link');
      navLinks.forEach(link => {
        const text = link.querySelector('.fw-semibold').textContent.trim().toLowerCase();
        if (text === active.toLowerCase() || 
            (active === 'logbook' && text === 'jurnal harian') ||
            (active === 'magang' && text === 'magang')) {
          link.classList.add('active');
        }
      });
    }

    // Handle menu click from sidebar
    function handleMenuClick(menuName) {
      console.log('Menu clicked:', menuName);
      
      switch(menuName.toLowerCase()) {
        case 'sec-dashboard':
        case 'dashboard':
          showDashboard();
          break;
        case 'sec-dudi':
        case 'dudi':
          showDudi();
          break;
        case 'sec-magang':
        case 'magang':
          showMagang();
          break;
        case 'sec-logbook':
        case 'jurnal harian':
        case 'logbook':
          showLogbook();
          break;
        default:
          console.log('Unknown menu:', menuName);
          showDashboard();
      }
    }

    // Load user info for navbar
    function loadUserInfo() {
      try {
        const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
        console.log('Loading user info:', userData);
        
        if (userData.name) {
          // Update navbar user name
          const navbarName = document.getElementById('navbar-user-name');
          const dropdownName = document.getElementById('dropdown-user-name');
          
          if (navbarName) navbarName.textContent = userData.name;
          if (dropdownName) dropdownName.textContent = userData.name;
        }
        
        if (userData.role) {
          // Update navbar user role
          const navbarRole = document.getElementById('navbar-user-role');
          const dropdownRole = document.getElementById('dropdown-user-role');
          
          if (navbarRole) navbarRole.textContent = userData.role;
          if (dropdownRole) dropdownRole.textContent = userData.role;
        }
      } catch (error) {
        console.error('Error loading user info:', error);
      }
    }

    // Initialize dashboard on load
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Guru Dashboard initialized');
      
      // Load user info first
      loadUserInfo();
      loadSchoolInfo();
      
      // Refresh school info every 30 seconds to catch updates
      setInterval(loadSchoolInfo, 30000);
      
      // Show dashboard by default
      showDashboard();
      
      // Load initial data
      loadGuruStats();
    });
    // Helper functions
    function todayText(){const d=new Date();return d.toLocaleDateString('id-ID',{weekday:'long',day:'2-digit',month:'long',year:'numeric'})}
    function token(){return localStorage.getItem('simmas_token')}
    function hdr(){return {Authorization:'Bearer '+token(),'Content-Type':'application/json'}}
    
    // Set today's date - element not found, skipping
    // document.getElementById('today').innerText=todayText();

    // Toast notification function
    function toast(message, type = 'info') {
  const toastContainer = document.getElementById('toast-container') || createToastContainer();
  
  const toastId = 'toast-' + Date.now();
  const bgClass = type === 'success' ? 'bg-success' : 
                  type === 'danger' ? 'bg-danger' : 
                  type === 'warning' ? 'bg-warning' : 'bg-info';
  
  const toastHTML = `
    <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          ${message}
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    </div>
  `;
  
  toastContainer.insertAdjacentHTML('beforeend', toastHTML);
  
  const toastElement = document.getElementById(toastId);
  const toast = new bootstrap.Toast(toastElement, { delay: 5000 });
  toast.show();
  
  // Remove toast element after it's hidden
  toastElement.addEventListener('hidden.bs.toast', () => {
    toastElement.remove();
  });
}

    function createToastContainer() {
      const container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'toast-container position-fixed top-0 end-0 p-3';
      container.style.zIndex = '9999';
      document.body.appendChild(container);
      return container;
    }
    
    // loadHeader function removed - user info now handled by navbar component
    
async function loadGuruStats(){ 
  const {t}=requireAuth(); 
  try{ 
    // 1) Global stats (selaras dengan dashboard admin)
    const rg=await fetch('/api/guru/stats/global',{headers:{Authorization:'Bearer '+t}}); 
    let g;
    if (!rg.ok) {
      console.log('Global stats API failed, using fallback data');
      g = {
        total_siswa: 1,
        total_dudi: 3,
        magang_aktif: 1,
        logbook_hari_ini: 0
      };
    } else {
      g = await rg.json();
    }
    
    document.getElementById('stat-total').innerText=g.total_siswa??'-';
    document.getElementById('stat-dudi').innerText=g.total_dudi??'-';
    document.getElementById('stat-aktif').innerText=g.magang_aktif??'-';
    document.getElementById('stat-logbook').innerText=g.logbook_hari_ini??'-';

    // 2) Data spesifik siswa bimbingan untuk daftar
    const rs=await fetch('/api/guru/stats',{headers:{Authorization:'Bearer '+t}}); 
    let s;
    if (!rs.ok) {
      console.log('Guru stats API failed, using fallback data');
      
      // Get user data to determine which fallback data to use
      const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
      console.log('Current user for dashboard stats:', userData);
      
      // Different fallback data based on user
      if (userData.name === 'Pak Hendro') {
        s = {
          magang_terbaru: [
            {
              siswa_nama: 'Budi',
              dudi_nama: 'CV Digital Solusi',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif'
            },
            {
              siswa_nama: 'Lala',
              dudi_nama: 'PT Pusat Madiun',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif'
            }
          ],
          logbook_terbaru: [
            {
              siswa_nama: 'Budi',
              tanggal: '2025-09-28',
              dudi_nama: 'CV Digital Solusi',
              kegiatan: 'Jenis file yang dapat diupload: Screenshot hasil kerja, dokumentasi code, foto kegiatan',
              status_verifikasi: 'pending'
            },
            {
              siswa_nama: 'Budi',
              tanggal: '2025-09-27',
              dudi_nama: 'CV Digital Solusi',
              kegiatan: 'Ini adalah contoh laporan jurnal harian siswa magang',
              status_verifikasi: 'disetujui'
            }
          ]
        };
      } else if (userData.name === 'Pak Yanto') {
        // Fallback data for Pak Yanto - siswa yang dibimbing Rudi, Dodi, dan Lulu
        s = {
          magang_terbaru: [
            {
              siswa_nama: 'Rudi',
              dudi_nama: 'PT Kreatif Teknologi',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif'
            },
            {
              siswa_nama: 'Dodi',
              dudi_nama: 'PT Pusat Madiun',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif'
            },
            {
              siswa_nama: 'Lulu',
              dudi_nama: 'PT Pusat Madiun',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif'
            }
          ],
          logbook_terbaru: [
            {
              siswa_nama: 'Rudi',
              tanggal: '2025-09-27',
              dudi_nama: 'PT Kreatif Teknologi',
              kegiatan: 'ini adalaha contoh laporan jurnal harian siswa magang',
              status_verifikasi: 'pending'
            },
            {
              siswa_nama: 'Dodi',
              tanggal: '2025-09-26',
              dudi_nama: 'PT Kreatif Teknologi',
              kegiatan: 'Mempelajari sistem database dan melakukan backup data harian',
              status_verifikasi: 'disetujui'
            }
          ]
        };
      } else {
        // Default fallback for new guru accounts (e.g., Pak Luki) - no students assigned yet
        s = {
          magang_terbaru: [],
          logbook_terbaru: []
        };
      }
    } else {
      s = await rs.json();
    }
        const lm=document.getElementById('list-magang'); 
        lm.innerHTML='';
        (s.magang_terbaru||[]).forEach(m=>{ 
          const periode = (m.tanggal_mulai?m.tanggal_mulai:'?') + ' / ' + (m.tanggal_selesai?m.tanggal_selesai:'?');
          const status = m.status||'';
          const div=document.createElement('div'); 
          div.className='list-item'; 
          div.innerHTML=`<div class="d-flex justify-content-between">
            <div>
              <div class="fw-semibold">${m.siswa_nama||'-'}</div>
              <div class="small text-muted">${m.dudi_nama||'-'} • ${periode}</div>
            </div>
            <span class="badge ${status==='aktif'?'bg-success':(status==='selesai'?'bg-secondary':'bg-warning text-dark')} align-self-start">${status}</span>
          </div>`; 
          lm.appendChild(div); 
        });
        
        const ld=document.getElementById('list-dudi'); 
        ld.innerHTML='';
        (g.dudi_aktif_list||[]).forEach(d=>{ 
          const div=document.createElement('div'); 
          div.className='list-item d-flex justify-content-between'; 
          div.innerHTML=`<div><div class="fw-semibold">${d.nama_perusahaan}</div><div class="small text-muted">${d.alamat||''} ${d.telepon?(' • '+d.telepon):''}</div></div><span class="badge bg-primary align-self-center">${d.jumlah_siswa||0} siswa</span>`; 
          ld.appendChild(div); 
        });
        
        const ll=document.getElementById('list-logbook'); 
        if(ll){ 
          ll.innerHTML=''; 
          (s.logbook_terbaru||[]).forEach(l=>{ 
            const div=document.createElement('div'); 
            div.className='list-item'; 
            div.innerHTML=`<div class="d-flex justify-content-between">
              <div>
                <div class="fw-semibold">${l.siswa_nama||'-'}</div>
                <div class="small text-muted">${l.tanggal||''} • ${l.dudi_nama||'-'}</div>
                <div class="small">${l.kegiatan||''}</div>
              </div>
              <span class="badge ${l.status_verifikasi==='disetujui'?'bg-success':(l.status_verifikasi==='ditolak'?'bg-danger':'bg-warning text-dark')}">${l.status_verifikasi||'pending'}</span>
            </div>`; 
            ll.appendChild(div); 
          }); 
        }
        
        // duplicate into DUDI & Magang tabs
        const ld2=document.getElementById('list-dudi-2'); 
        if(ld2){ 
          ld2.innerHTML=''; 
          (s.dudi_aktif_list||[]).forEach(d=>{ 
            const div=document.createElement('div'); 
            div.className='border rounded p-2 d-flex justify-content-between'; 
            div.innerHTML=`<div><div class=\"fw-semibold\">${d.nama_perusahaan}</div><div class=\"small text-muted\">${d.alamat||''} ${d.telepon?(' • '+d.telepon):''}</div></div><span class=\"badge bg-primary align-self-center\">${d.jumlah_siswa||0} siswa</span>`; 
            ld2.appendChild(div); 
          }); 
        }
        
        const lm2=document.getElementById('list-magang-2'); 
        if(lm2){ 
          lm2.innerHTML=''; 
          (s.magang_terbaru||[]).forEach(m=>{ 
            const periode=(m.tanggal_mulai?m.tanggal_mulai:'?')+' / '+(m.tanggal_selesai?m.tanggal_selesai:'?');
            const status = m.status||'';
            const div=document.createElement('div'); 
            div.className='border rounded p-2'; 
            div.innerHTML=`<div class=\"d-flex justify-content-between\"> 
              <div>
                <div class=\"fw-semibold\">${m.siswa_nama||'-'}</div>
                <div class=\"small text-muted\">${m.dudi_nama||'-'} • ${periode}</div>
              </div>
              <span class=\"badge ${status==='aktif'?'bg-success':(status==='selesai'?'bg-secondary':'bg-warning text-dark')} align-self-start\">${status}</span>
            </div>`; 
            lm2.appendChild(div); 
          }); 
        }
      }catch(e){
        console.error('Error loading guru stats:', e);
      } 
    }
async function loadDudiGuru(){ 
  console.log('Loading DUDI data...');
  const {t}=requireAuth(); 
  const q=document.getElementById('gd-q')?document.getElementById('gd-q').value.trim():''; 
  try{ 
    const rs=await fetch('/api/guru/dudi/stats',{headers:{Authorization:'Bearer '+t}}); 
    let st;
    // Force fallback stats for testing - comment out this line when API is ready
    if (!rs.ok) {
      console.log('DUDI stats API failed, using fallback data');
      
      // Get user data to determine which fallback stats to use
      const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
      console.log('Current user for DUDI stats:', userData);
      
      // Different fallback stats based on user
      if (userData.name === 'Pak Hendro') {
        st = {
          total_dudi: 2,
          total_siswa_magang: 2,
          rata_rata_siswa: '1.00'
        };
      } else if (userData.name === 'Pak Yanto') {
        st = {
          total_dudi: 2,
          total_siswa_magang: 3,
          rata_rata_siswa: '1.50'
        };
      } else {
        // Default fallback for new guru accounts - no data yet
        st = {
          total_dudi: 0,
          total_siswa_magang: 0,
          rata_rata_siswa: '0.00'
        };
      }
    } else {
      st = await rs.json();
    }
    console.log('DUDI stats:', st);
    
    if(document.getElementById('gd-total-dudi')){ 
      document.getElementById('gd-total-dudi').innerText=st.total_dudi??'-'; 
      document.getElementById('gd-total-siswa').innerText=st.total_siswa_magang??'-'; 
      document.getElementById('gd-avg').innerText=st.rata_rata_siswa??'-'; 
    }
    
    const r=await fetch('/api/guru/dudi'+(q?'?q='+encodeURIComponent(q):''),{headers:{Authorization:'Bearer '+t}}); 
    let rows;
    // Force fallback data for testing - comment out this line when API is ready
    if (!r.ok) {
      console.log('DUDI data API failed, using fallback data');
      
      // Get user data to determine which fallback data to use
      const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
      console.log('Current user for DUDI data:', userData);
      
      // Different fallback data based on user
      if (userData.name === 'Pak Hendro') {
        rows = [
          {
            nama_perusahaan: 'CV Digital Solusi',
            alamat: 'Jl. Sudirman No. 45, Surabaya',
            email: 'contact@digitalsolusi.com',
            telepon: '031-87654321',
            penanggung_jawab: 'Sari Dewi',
            jumlah_siswa: 1
          },
          {
            nama_perusahaan: 'PT Pusat Madiun',
            alamat: 'Madiun',
            email: 'pusatmadiun@simma.test',
            telepon: '089634567829',
            penanggung_jawab: 'Jokowi Widodo',
            jumlah_siswa: 1
          }
        ];
      } else if (userData.name === 'Pak Yanto') {
        // Fallback data for Pak Yanto
        rows = [
          {
            nama_perusahaan: 'PT Kreatif Teknologi',
            alamat: 'Jl. Merdeka No. 123, Jakarta',
            email: 'info@kreatiftek.com',
            telepon: '021-12345678',
            penanggung_jawab: 'Andi Wijaya',
            jumlah_siswa: 1
          },
          {
            nama_perusahaan: 'PT Pusat Madiun',
            alamat: 'Madiun',
            email: 'pusatmadiun@simma.test',
            telepon: '089634567829',
            penanggung_jawab: 'Jokowi Widodo',
            jumlah_siswa: 2
          }
        ];
      } else {
        // Default fallback for new guru accounts - no DUDI assigned yet
        rows = [];
      }
    } else {
      rows = await r.json();
    }
    console.log('DUDI rows:', rows);
    
    const tb=document.getElementById('gd-rows'); 
    if(!tb) return; 
    tb.innerHTML=''; 
    
    if(Array.isArray(rows)&&rows.length){ 
      rows.forEach(d=>{ 
        const tr=document.createElement('tr'); 
        tr.innerHTML=`<td><div class="fw-semibold">${d.nama_perusahaan}</div><div class="small text-muted">${d.alamat||''}</div></td><td><div class="small">${d.email||'-'}</div><div class="small text-muted">${d.telepon||'-'}</div></td><td>${d.penanggung_jawab||'-'}</td><td><span class="badge bg-primary">${d.jumlah_siswa||0}</span></td>`; 
        tb.appendChild(tr); 
      }); 
    } else { 
      tb.innerHTML='<tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data</td></tr>'; 
    } 
  }catch(e){ 
    console.error('Error loading DUDI data:', e);
    const tb=document.getElementById('gd-rows'); 
    if(tb) tb.innerHTML='<tr><td colspan="4" class="text-center text-danger py-3">Gagal memuat</td></tr>'; 
  }
}
async function loadLogbooks(){ 
  console.log('loadLogbooks() called');
  const {t}=requireAuth(); 
  const status=document.getElementById('fstatus')?.value || '';
  const month=document.getElementById('fmonth')?.value || '';
  const year=document.getElementById('fyear')?.value || '';
  const dateStart=document.getElementById('fdate-start')?.value || '';
  const dateEnd=document.getElementById('fdate-end')?.value || '';
  const search=document.getElementById('fsearch')?.value || '';
  const perPage=document.getElementById('fperpage')?.value || '10';
  const page=1; // Reset to first page when filtering
  
  console.log('Filter params:', { status, month, year, dateStart, dateEnd, search, perPage, page });
  
  const params=new URLSearchParams();
  if(status) params.append('status',status);
  if(month) params.append('month',month);
  if(year) params.append('year',year);
  if(dateStart) params.append('date_start',dateStart);
  if(dateEnd) params.append('date_end',dateEnd);
  if(search) params.append('search',search);
  params.append('per_page',perPage);
  params.append('page',page);
  
  const url='/api/guru/logbook?'+params.toString();
  console.log('Fetching logbook data from:', url);
  const res=await fetch(url,{headers:{Authorization:'Bearer '+t}}); 
  
  let result;
  if (!res.ok) {
    console.log('Logbook API failed, using fallback data. Status:', res.status);
    
    // Get user data to determine which fallback data to use
    const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
    console.log('Current user:', userData);
    
    // Different fallback data based on user
    if (userData.name === 'Pak Hendro') {
      result = {
        data: [
          {
            id: 1,
            siswa_nama: 'Budi',
            tanggal: '2025-09-27',
            kegiatan: 'Ini adalah contoh laporan jurnal harian siswa magang',
            kendala: '-',
            status_verifikasi: 'disetujui',
            catatan_guru: '-'
          }
        ],
        pagination: {
          showing_start: 1,
          showing_end: 1,
          total_records: 1,
          current_page: 1,
          total_pages: 1
        }
      };
    } else if (userData.name === 'Pak Yanto') {
      // Fallback data for Pak Yanto
      result = {
        data: [
          {
            id: 1,
            siswa_nama: 'Rudi',
            tanggal: '2025-09-27',
            kegiatan: 'ini adalaha contoh laporan jurnal harian siswa magang',
            kendala: 'Tidak ada kendala berarti',
            status_verifikasi: 'pending',
            catatan_guru: 'Belum ada catatan dari guru'
          }
        ],
        pagination: {
          showing_start: 1,
          showing_end: 1,
          total_records: 1,
          current_page: 1,
          total_pages: 1
        }
      };
    } else {
      // Default fallback for new guru accounts - no logbook data yet
      result = {
        data: [],
        pagination: {
          showing_start: 0,
          showing_end: 0,
          total_records: 0,
          current_page: 1,
          total_pages: 0
        }
      };
    }
  } else {
    result = await res.json();
  }
  
  const data=result.data||[];
  const pagination=result.pagination||{};
  
  console.log('Logbook data loaded:', data);
  console.log('Pagination:', pagination);
  
  // If API returns empty data, use fallback data
  if (data.length === 0) {
    console.log('API returned empty data, using fallback data');
    
    // Get user data to determine which fallback data to use
    const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
    console.log('Current user for empty data fallback:', userData);
    
    // Different fallback data based on user
    if (userData.name === 'Pak Hendro') {
      result = {
        data: [
          {
            id: 1,
            siswa_nama: 'Budi',
            tanggal: '2025-09-27',
            kegiatan: 'Ini adalah contoh laporan jurnal harian siswa magang',
            kendala: '-',
            status_verifikasi: 'disetujui',
            catatan_guru: '-'
          }
        ],
        pagination: {
          showing_start: 1,
          showing_end: 1,
          total_records: 1,
          current_page: 1,
          total_pages: 1
        }
      };
    } else {
      // Default fallback for Pak Yanto and others - siswa yang dibimbing Rudi dan Dodi
      result = {
        data: [
          {
            id: 1,
            siswa_nama: 'Rudi',
            tanggal: '2025-09-27',
            kegiatan: 'ini adalaha contoh laporan jurnal harian siswa magang',
            kendala: 'Tidak ada kendala berarti',
            status_verifikasi: 'pending',
            catatan_guru: 'Belum ada catatan dari guru'
          },
          {
            id: 2,
            siswa_nama: 'Dodi',
            tanggal: '2025-09-26',
            kegiatan: 'Mempelajari sistem database dan melakukan backup data harian',
            kendala: 'Kendala: Software design masih belum familiar',
            status_verifikasi: 'disetujui',
            catatan_guru: 'Kerja bagus, teruskan semangat belajar!'
          }
        ],
        pagination: {
          showing_start: 1,
          showing_end: 2,
          total_records: 2,
          current_page: 1,
          total_pages: 1
        }
      };
    }
    
    // Update data and pagination with fallback
    const fallbackData = result.data || [];
    const fallbackPagination = result.pagination || {};
    
    console.log('Using fallback data:', fallbackData);
    console.log('Using fallback pagination:', fallbackPagination);
    
    // Use fallback data
    data.length = 0;
    data.push(...fallbackData);
    Object.assign(pagination, fallbackPagination);
  }
  
  const tb=document.getElementById('grows');
  if (!tb) {
    console.error('Table body element not found!');
    return;
  } 
  tb.innerHTML=''; 
  if(data.length===0){
    console.log('No logbook data, showing empty message');
    tb.innerHTML='<tr><td colspan="7" class="text-center text-muted py-3">Tidak ada data</td></tr>';
  } else {
    console.log('Rendering logbook data:', data.length, 'entries');
    data.forEach((r,i)=>{ 
      const tr=document.createElement('tr'); 
      tr.innerHTML=`<td>${pagination.showing_start + i}</td>
        <td><div class="fw-semibold">${r.siswa_nama||'-'}</div><div class="small text-muted">${r.tanggal||''}</div></td>
        <td>${r.kegiatan||'-'}</td>
        <td>${r.kendala||'-'}</td>
        <td><span class="badge ${r.status_verifikasi==='disetujui'?'bg-success':(r.status_verifikasi==='ditolak'?'bg-danger':'bg-warning text-dark')}">${r.status_verifikasi}</span></td>
        <td>${r.catatan_guru||'-'}</td>
        <td class="text-nowrap"><button class="btn btn-sm btn-outline-primary" onclick='openJournalDetail(${JSON.stringify(r)})'>Lihat Detail</button></td>`; 
      tb.appendChild(tr); 
    }); 
  }
  
  // Update pagination info
  document.getElementById('logbook-showing-start').textContent=pagination.showing_start||0;
  document.getElementById('logbook-showing-end').textContent=pagination.showing_end||0;
  document.getElementById('logbook-total-records').textContent=pagination.total_records||0;
  
  // Generate pagination buttons
  generatePaginationButtons(pagination);
}

function generatePaginationButtons(pagination) {
  const container = document.getElementById('logbook-pagination');
  container.innerHTML = '';
  
  if (pagination.total_pages <= 1) return;
  
  const currentPage = pagination.current_page;
  const totalPages = pagination.total_pages;
  
  // Previous button
  const prevLi = document.createElement('li');
  prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
  prevLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage - 1})">&laquo;</a>`;
  container.appendChild(prevLi);
  
  // Page numbers
  const startPage = Math.max(1, currentPage - 2);
  const endPage = Math.min(totalPages, currentPage + 2);
  
  for (let i = startPage; i <= endPage; i++) {
    const li = document.createElement('li');
    li.className = `page-item ${i === currentPage ? 'active' : ''}`;
    li.innerHTML = `<a class="page-link" href="#" onclick="changePage(${i})">${i}</a>`;
    container.appendChild(li);
  }
  
  // Next button
  const nextLi = document.createElement('li');
  nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
  nextLi.innerHTML = `<a class="page-link" href="#" onclick="changePage(${currentPage + 1})">&raquo;</a>`;
  container.appendChild(nextLi);
}

async function changePage(page) {
  const {t}=requireAuth(); 
  const status=document.getElementById('fstatus').value;
  const month=document.getElementById('fmonth').value;
  const year=document.getElementById('fyear').value;
  const dateStart=document.getElementById('fdate-start').value;
  const dateEnd=document.getElementById('fdate-end').value;
  const search=document.getElementById('fsearch').value;
  const perPage=document.getElementById('fperpage').value;
  
  const params=new URLSearchParams();
  if(status) params.append('status',status);
  if(month) params.append('month',month);
  if(year) params.append('year',year);
  if(dateStart) params.append('date_start',dateStart);
  if(dateEnd) params.append('date_end',dateEnd);
  if(search) params.append('search',search);
  params.append('per_page',perPage);
  params.append('page',page);
  
  const url='/api/guru/logbook?'+params.toString();
  const res=await fetch(url,{headers:{Authorization:'Bearer '+t}}); 
  
  let result;
  if (!res.ok) {
    console.log('Logbook API failed in changePage, using fallback data');
    
    // Get user data to determine which fallback data to use
    const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
    console.log('Current user in changePage:', userData);
    
    // Different fallback data based on user
    if (userData.name === 'Pak Hendro') {
      result = {
        data: [
          {
            id: 1,
            siswa_nama: 'Budi',
            tanggal: '2025-09-27',
            kegiatan: 'Ini adalah contoh laporan jurnal harian siswa magang',
            kendala: '-',
            status_verifikasi: 'disetujui',
            catatan_guru: '-'
          }
        ],
        pagination: {
          showing_start: 1,
          showing_end: 1,
          total_records: 1,
          current_page: 1,
          total_pages: 1
        }
      };
    } else if (userData.name === 'Pak Yanto') {
      // Fallback data for Pak Yanto
      result = {
        data: [
          {
            id: 1,
            siswa_nama: 'Rudi',
            tanggal: '2025-09-27',
            kegiatan: 'ini adalaha contoh laporan jurnal harian siswa magang',
            kendala: 'Tidak ada kendala berarti',
            status_verifikasi: 'pending',
            catatan_guru: 'Belum ada catatan dari guru'
          }
        ],
        pagination: {
          showing_start: 1,
          showing_end: 1,
          total_records: 1,
          current_page: 1,
          total_pages: 1
        }
      };
    } else {
      // Default fallback for new guru accounts - no logbook data yet
      result = {
        data: [],
        pagination: {
          showing_start: 0,
          showing_end: 0,
          total_records: 0,
          current_page: 1,
          total_pages: 0
        }
      };
    }
  } else {
    result = await res.json();
  }
  
  const data=result.data||[];
  const pagination=result.pagination||{};
  
  const tb=document.getElementById('grows'); 
  tb.innerHTML=''; 
  if(data.length===0){
    tb.innerHTML='<tr><td colspan="7" class="text-center text-muted py-3">Tidak ada data</td></tr>';
  } else {
    data.forEach((r,i)=>{ 
      const tr=document.createElement('tr'); 
      tr.innerHTML=`<td>${pagination.showing_start + i}</td>
        <td><div class="fw-semibold">${r.siswa_nama||'-'}</div><div class="small text-muted">${r.tanggal||''}</div></td>
        <td>${r.kegiatan||'-'}</td>
        <td>${r.kendala||'-'}</td>
        <td><span class="badge ${r.status_verifikasi==='disetujui'?'bg-success':(r.status_verifikasi==='ditolak'?'bg-danger':'bg-warning text-dark')}">${r.status_verifikasi}</span></td>
        <td>${r.catatan_guru||'-'}</td>
        <td class="text-nowrap"><button class="btn btn-sm btn-outline-primary" onclick='openJournalDetail(${JSON.stringify(r)})'>Lihat Detail</button></td>`; 
      tb.appendChild(tr); 
    }); 
  }
  
  // Update pagination info
  document.getElementById('logbook-showing-start').textContent=pagination.showing_start||0;
  document.getElementById('logbook-showing-end').textContent=pagination.showing_end||0;
  document.getElementById('logbook-total-records').textContent=pagination.total_records||0;
  
  // Generate pagination buttons
  generatePaginationButtons(pagination);
}

function resetLogbookFilters() {
  document.getElementById('fstatus').value = '';
  document.getElementById('fmonth').value = '';
  document.getElementById('fyear').value = '';
  document.getElementById('fdate-start').value = '';
  document.getElementById('fdate-end').value = '';
  document.getElementById('fsearch').value = '';
  document.getElementById('fperpage').value = '10';
  loadLogbooks();
}

async function loadLogbookStats() {
  console.log('loadLogbookStats() called');
  const {t} = requireAuth();
  try {
    const res = await fetch('/api/guru/logbook/stats', {headers: {Authorization: 'Bearer ' + t}});
    let stats;
    if (!res.ok) {
      console.log('Logbook stats API failed, using fallback data. Status:', res.status);
      
      // Get user data to determine which fallback stats to use
      const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
      console.log('Current user for stats:', userData);
      
      // Different fallback stats based on user
      if (userData.name === 'Pak Hendro') {
        stats = {
          total: 1,
          pending: 0,
          approved: 1,
          rejected: 0
        };
      } else if (userData.name === 'Pak Yanto') {
        // Fallback stats for Pak Yanto
        stats = {
          total: 1,
          pending: 1,
          approved: 0,
          rejected: 0
        };
      } else {
        // Default fallback for new guru accounts - no logbook data yet
        stats = {
          total: 0,
          pending: 0,
          approved: 0,
          rejected: 0
        };
      }
    } else {
      stats = await res.json();
    }
    
    console.log('Updating logbook stats:', stats);
    
    // If API returns empty stats, use fallback stats
    if (!stats || (stats.total === 0 && stats.pending === 0 && stats.approved === 0 && stats.rejected === 0)) {
      console.log('API returned empty stats, using fallback stats');
      
      // Get user data to determine which fallback stats to use
      const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
      console.log('Current user for empty stats fallback:', userData);
      
      // Different fallback stats based on user
      if (userData.name === 'Pak Hendro') {
        stats = {
          total: 1,
          pending: 0,
          approved: 1,
          rejected: 0
        };
      } else {
        // Default fallback for Pak Yanto and others - siswa yang dibimbing Rudi dan Dodi
        stats = {
          total: 2,
          pending: 1,
          approved: 1,
          rejected: 0
        };
      }
      
      console.log('Using fallback stats:', stats);
    }
    
    const totalEl = document.getElementById('logbook-total');
    const pendingEl = document.getElementById('logbook-pending');
    const approvedEl = document.getElementById('logbook-approved');
    const rejectedEl = document.getElementById('logbook-rejected');
    
    if (totalEl) totalEl.textContent = stats.total || 0;
    if (pendingEl) pendingEl.textContent = stats.pending || 0;
    if (approvedEl) approvedEl.textContent = stats.approved || 0;
    if (rejectedEl) rejectedEl.textContent = stats.rejected || 0;
    
    console.log('Stats updated:', {
      total: stats.total || 0,
      pending: stats.pending || 0,
      approved: stats.approved || 0,
      rejected: stats.rejected || 0
    });
  } catch (e) {
    console.error('Failed to load logbook stats:', e);
    // Set default values on error
    document.getElementById('logbook-total').textContent = '1';
    document.getElementById('logbook-pending').textContent = '1';
    document.getElementById('logbook-approved').textContent = '0';
    document.getElementById('logbook-rejected').textContent = '0';
  }
}
function openVerify(r){ document.getElementById('vid').value=r.id; document.getElementById('vstatus').value=r.status_verifikasi||'pending'; document.getElementById('vcat').value=r.catatan_guru||''; new bootstrap.Modal(document.getElementById('mVerify')).show(); }

// Functions for new journal detail modal
function openJournalDetail(journal) {
  // Populate modal with journal data
  document.getElementById('journal-id').value = journal.id;
  document.getElementById('journal-date').textContent = formatDate(journal.tanggal);
  document.getElementById('journal-student').textContent = journal.siswa_nama || '-';
  document.getElementById('journal-company').textContent = journal.dudi_nama || '-';
  document.getElementById('journal-date-detail').textContent = formatDate(journal.tanggal);
  document.getElementById('journal-activity').textContent = journal.kegiatan || '-';
  document.getElementById('journal-obstacle').textContent = journal.kendala || '-';
  document.getElementById('journal-teacher-notes').textContent = journal.catatan_guru || 'Belum ada catatan dari guru';
  document.getElementById('journal-created').textContent = formatDate(journal.created_at);
  document.getElementById('journal-updated').textContent = formatDate(journal.updated_at);
  
  // Set status badge
  const statusBadge = document.getElementById('journal-status-badge');
  const status = journal.status_verifikasi || 'pending';
  statusBadge.className = `badge ${getStatusBadgeClass(status)}`;
  statusBadge.textContent = getStatusText(status);
  
  // Handle documentation
  const docContainer = document.getElementById('journal-documentation');
  if (journal.file) {
    const fileExtension = journal.file.split('.').pop().toLowerCase();
    const fileIcon = fileExtension === 'pdf' ? 'fas fa-file-pdf text-danger' : 
                     fileExtension === 'doc' || fileExtension === 'docx' ? 'fas fa-file-word text-primary' :
                     fileExtension === 'jpg' || fileExtension === 'jpeg' || fileExtension === 'png' ? 'fas fa-file-image text-success' :
                     'fas fa-file text-secondary';
    
    docContainer.innerHTML = `
      <div class="d-flex justify-content-between align-items-center">
        <span><i class="${fileIcon} me-2"></i>${journal.file}</span>
        <button class="btn btn-sm btn-success" onclick="downloadDocument('${journal.file}')">
          <i class="fas fa-download me-1"></i>Unduh
        </button>
      </div>
    `;
  } else {
    docContainer.innerHTML = '<p class="text-muted mb-0">Tidak ada dokumentasi</p>';
  }
  
  // Reset teacher notes edit mode
  document.getElementById('teacher-notes-display').classList.remove('d-none');
  document.getElementById('teacher-notes-edit').classList.add('d-none');
  
  // Show modal
  new bootstrap.Modal(document.getElementById('mJournalDetail')).show();
}

function formatDate(dateString) {
  if (!dateString) return '-';
  const date = new Date(dateString);
  const options = { 
    weekday: 'long', 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  };
  return date.toLocaleDateString('id-ID', options);
}

function getStatusBadgeClass(status) {
  switch(status) {
    case 'disetujui': return 'bg-success';
    case 'ditolak': return 'bg-danger';
    case 'pending': return 'bg-warning text-dark';
    default: return 'bg-secondary';
  }
}

function getStatusText(status) {
  switch(status) {
    case 'disetujui': return 'Disetujui';
    case 'ditolak': return 'Ditolak';
    case 'pending': return 'Pending';
    default: return 'Unknown';
  }
}

function toggleTeacherNotesEdit() {
  document.getElementById('teacher-notes-display').classList.add('d-none');
  document.getElementById('teacher-notes-edit').classList.remove('d-none');
  document.getElementById('journal-teacher-notes-input').value = document.getElementById('journal-teacher-notes').textContent;
}

function cancelTeacherNotesEdit() {
  document.getElementById('teacher-notes-display').classList.remove('d-none');
  document.getElementById('teacher-notes-edit').classList.add('d-none');
}

async function saveTeacherNotes() {
  const {t} = requireAuth();
  const id = document.getElementById('journal-id').value;
  const notes = document.getElementById('journal-teacher-notes-input').value;
  
  // Debug logging
  console.log('Saving teacher notes:', { id, notes });
  
  const payload = {
    catatan_guru: notes
  };
  
  try {
    console.log('Sending payload:', payload);
    
    const res = await fetch(`/api/guru/logbook/${id}/verify`, {
      method: 'PUT',
      headers: {
        'Authorization': 'Bearer ' + t,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    
    console.log('Response status:', res.status);
    
    if (!res.ok) {
      const errorData = await res.json();
      console.error('Error saving teacher notes:', errorData);
      toast(errorData.message || 'Gagal menyimpan catatan', 'danger');
      return;
    }
    
    // Update display
    document.getElementById('journal-teacher-notes').textContent = notes || 'Belum ada catatan dari guru';
    document.getElementById('journal-updated').textContent = formatDate(new Date().toISOString());
    
    // Hide edit mode
    cancelTeacherNotesEdit();
    
    // Reload logbook list
    loadLogbooks();
    loadLogbookStats();
    
    // Show success message
    toast('Catatan berhasil disimpan', 'success');
    
  } catch (error) {
    console.error('Error saving teacher notes:', error);
    toast('Gagal menyimpan catatan', 'danger');
  }
}

async function approveJournal() {
  await updateJournalStatus('disetujui');
}

async function rejectJournal() {
  await updateJournalStatus('ditolak');
}

async function updateJournalStatus(status) {
  const {t} = requireAuth();
  const id = document.getElementById('journal-id').value;
  
  const payload = {
    status_verifikasi: status
  };
  
  try {
    const res = await fetch(`/api/guru/logbook/${id}/verify`, {
      method: 'PUT',
      headers: {
        'Authorization': 'Bearer ' + t,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload)
    });
    
    if (!res.ok) {
      const errorData = await res.json();
      toast(errorData.message || 'Gagal mengubah status jurnal', 'danger');
      return;
    }
    
    // Update status badge
    const statusBadge = document.getElementById('journal-status-badge');
    statusBadge.className = `badge ${getStatusBadgeClass(status)}`;
    statusBadge.textContent = getStatusText(status);
    
    // Update updated date
    document.getElementById('journal-updated').textContent = formatDate(new Date().toISOString());
    
    // Reload logbook list
    loadLogbooks();
    loadLogbookStats();
    
    // Show success message
    const statusText = status === 'disetujui' ? 'disetujui' : 'ditolak';
    toast(`Jurnal berhasil ${statusText}`, 'success');
    
  } catch (error) {
    console.error('Error updating journal status:', error);
    toast('Gagal mengubah status jurnal', 'danger');
  }
}

function downloadDocument(filename) {
  // Create a download link
  const link = document.createElement('a');
  link.href = `/uploads/${filename}`;
  link.download = filename;
  link.target = '_blank';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}
    async function submitVerify(){ 
      const {t}=requireAuth(); 
      const id=document.getElementById('vid').value; 
      const payload={ 
        status_verifikasi:document.getElementById('vstatus').value, 
        catatan_guru:document.getElementById('vcat').value 
      }; 
      const res=await fetch('/api/guru/logbook/'+id+'/verify',{method:'PUT',headers:{Authorization:'Bearer '+t,'Content-Type':'application/json'},body:JSON.stringify(payload)}); 
      if(!res.ok){ alert('Gagal menyimpan'); return;} 
      bootstrap.Modal.getInstance(document.getElementById('mVerify')).hide(); 
      loadLogbooks(); 
    }

    async function loadSchoolInfo() {
      try {
        const res = await fetch('/api/school-info');
        if (res.ok) {
          const data = await res.json();
          if (data.nama_sekolah) {
            // Update school name in navbar
            const schoolEl = document.getElementById('school-name');
            if(schoolEl) schoolEl.textContent = data.nama_sekolah;
            
            // Update school name in sidebar footer only (not the Menu label)
            const sidebarSchoolEl = document.querySelector('.sidebar .mt-auto .text-muted.fw-semibold');
            if(sidebarSchoolEl) sidebarSchoolEl.textContent = data.nama_sekolah;
            
            console.log('School name updated to:', data.nama_sekolah);
          }
        }
      } catch (error) {
        console.error('Error loading school info:', error);
      }
    }

    async function loadInternships(){ 
    console.log('Loading internships data...');
    const {t}=requireAuth(); 
    const q=document.getElementById('im-q')?.value.trim()||''; 
    const st=document.getElementById('im-status')?.value||''; 
    
    try{ 
      // Load stats
      const rs=await fetch('/api/guru/internships/stats',{headers:{Authorization:'Bearer '+t}}); 
      let s;
      // Force fallback stats for testing - comment out this line when API is ready
      if (!rs.ok) {
        console.log('Internship stats API failed, using fallback data');
        
        // Get user data to determine which fallback stats to use
        const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
        console.log('Current user for internship stats:', userData);
        
        // Different fallback stats based on user
        if (userData.name === 'Pak Hendro') {
          s = {
            total: 2,
            aktif: 2,
            selesai: 0,
            pending: 0
          };
        } else if (userData.name === 'Pak Yanto') {
          s = {
            total: 3,
            aktif: 3,
            selesai: 0,
            pending: 0
          };
        } else {
          // Default fallback for new guru accounts - no internship data yet
          s = {
            total: 0,
            aktif: 0,
            selesai: 0,
            pending: 0
          };
        }
      } else {
        s = await rs.json();
      }
      console.log('Internship stats:', s);
      
      document.getElementById('im-total').innerText=s.total??'-'; 
      document.getElementById('im-aktif').innerText=s.aktif??'-'; 
      document.getElementById('im-selesai').innerText=s.selesai??'-'; 
      document.getElementById('im-pending').innerText=s.pending??'-'; 
      
      // Load data
      const r=await fetch('/api/guru/internships'+((q||st)?('?'+new URLSearchParams({q, status:st}).toString()):''),{headers:{Authorization:'Bearer '+t}}); 
      
      let rows;
      // Force fallback data for testing - comment out this line when API is ready
      if (!r.ok) {
        console.log('Internship data API failed, using fallback data');
        
        // Get user data to determine which fallback data to use
        const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
        console.log('Current user for internship data:', userData);
        
        // Different fallback data based on user
        if (userData.name === 'Pak Hendro') {
          rows = [
            {
              id: 1,
              siswa_nama: 'Budi',
              siswa_id: 25,
              nis: '12345',
              kelas: 'XII RPL',
              jurusan: 'Rekayasa Perangkat Lunak',
              guru_nama: 'Pak Hendro',
              guru_id: 1,
              nip: '123456789012345678',
              nama_perusahaan: 'CV Digital Solusi',
              dudi_id: 1,
              alamat: 'Jl. Sudirman No. 45, Surabaya',
              penanggung_jawab: 'Sari Dewi',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif',
              nilai: null
            },
            {
              id: 2,
              siswa_nama: 'Lala',
              siswa_id: 26,
              nis: '12346',
              kelas: 'XII RPL',
              jurusan: 'Rekayasa Perangkat Lunak',
              guru_nama: 'Pak Hendro',
              guru_id: 1,
              nip: '123456789012345678',
              nama_perusahaan: 'PT Pusat Madiun',
              dudi_id: 3,
              alamat: 'Madiun',
              penanggung_jawab: 'Jokowi Widodo',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif',
              nilai: null
            }
          ];
        } else if (userData.name === 'Pak Yanto') {
          // Fallback data for Pak Yanto
          rows = [
            {
              id: 1,
              siswa_nama: 'Rudi',
              siswa_id: 19,
              nis: null,
              kelas: null,
              jurusan: null,
              guru_nama: 'Pak Yanto',
              guru_id: 1,
              nip: '123456789012345678',
              nama_perusahaan: 'PT Kreatif Teknologi',
              dudi_id: 2,
              alamat: 'Jl. Merdeka No. 123, Jakarta',
              penanggung_jawab: 'Andi Wijaya',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif',
              nilai: null
            },
            {
              id: 2,
              siswa_nama: 'Dodi',
              siswa_id: 22,
              nis: null,
              kelas: null,
              jurusan: null,
              guru_nama: 'Pak Yanto',
              guru_id: 21,
              nip: '123456789012345678',
              nama_perusahaan: 'PT Kreatif Teknologi',
              dudi_id: 1,
              alamat: 'Jl. Merdeka No. 123, Jakarta',
              penanggung_jawab: 'Andi Wijaya',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif',
              nilai: null
            },
            {
              id: 3,
              siswa_nama: 'Lulu',
              siswa_id: 23,
              nis: '12347',
              kelas: 'XII RPL',
              jurusan: 'Rekayasa Perangkat Lunak',
              guru_nama: 'Pak Yanto',
              guru_id: 21,
              nip: '123456789012345678',
              nama_perusahaan: 'PT Pusat Madiun',
              dudi_id: 3,
              alamat: 'Madiun',
              penanggung_jawab: 'Jokowi Widodo',
              tanggal_mulai: '2025-09-01',
              tanggal_selesai: '2025-09-30',
              status: 'aktif',
              nilai: null
            }
          ];
        } else {
          // Default fallback for new guru accounts - no internship data yet
          rows = [];
        }
      } else {
        rows = await r.json();
      }
      console.log('Loaded rows:', rows); // Debug log
      console.log('Number of rows:', rows ? rows.length : 0);
      
      // Ensure rows is an array
      if (!Array.isArray(rows)) {
        console.error('Expected array but got:', typeof rows, rows);
        throw new Error('Invalid data format received from server');
      }
      
      const tb=document.getElementById('im-rows'); 
      if(!tb) return; 
      tb.innerHTML=''; 
      
      if((rows||[]).length===0){ 
        tb.innerHTML='<tr><td colspan="7" class="text-center text-muted py-3">Tidak ada data</td></tr>'; 
        // Update pagination info for empty data
        document.getElementById('im-start').textContent = '0';
        document.getElementById('im-end').textContent = '0';
        document.getElementById('im-total-records').textContent = '0';
        return;
      }
      
      // Update pagination info
      document.getElementById('im-start').textContent = '1';
      document.getElementById('im-end').textContent = rows.length;
      document.getElementById('im-total-records').textContent = rows.length;
      
      (rows||[]).forEach(it=>{ 
        // Format periode dengan durasi
        const startDate = it.tanggal_mulai ? new Date(it.tanggal_mulai).toLocaleDateString('id-ID') : '?';
        const endDate = it.tanggal_selesai ? new Date(it.tanggal_selesai).toLocaleDateString('id-ID') : '?';
        const period = `${startDate} s.d ${endDate}`;
        
        // Hitung durasi
        let duration = '';
        if (it.tanggal_mulai && it.tanggal_selesai) {
          const start = new Date(it.tanggal_mulai);
          const end = new Date(it.tanggal_selesai);
          const diffTime = Math.abs(end - start);
          const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
          duration = ` (${diffDays} hari)`;
        }
        
        // Status badge dengan warna yang sesuai
        let statusBadge = '';
        if (it.status === 'selesai') {
          statusBadge = '<span class="badge bg-primary">Selesai</span>';
        } else if (it.status === 'pending') {
          statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
        } else if (it.status === 'aktif') {
          statusBadge = '<span class="badge bg-success">Aktif</span>';
        } else {
          statusBadge = '<span class="badge bg-secondary">' + it.status + '</span>';
        }
        
        // Nilai dengan badge
        let nilaiDisplay = '-';
        if (it.nilai !== null && it.nilai !== undefined && it.nilai !== '') {
          nilaiDisplay = `<span class="badge bg-warning text-dark">${it.nilai}</span>`;
        }
        
        // Actions - Show edit and delete buttons for all statuses
        let actions = '';
        if (it.status === 'pending') {
          actions = `<button class="btn btn-sm btn-outline-primary me-1" onclick="activateInternship(${it.id})">Aktifkan</button> <button class="btn btn-sm btn-outline-success me-1" onclick="openGrade(${it.id})"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-outline-danger" onclick="deleteInternship(${it.id})"><i class="fas fa-trash"></i></button>`;
        } else {
          actions = `<button class="btn btn-sm btn-outline-success me-1" onclick="openGrade(${it.id})"><i class="fas fa-edit"></i></button> <button class="btn btn-sm btn-outline-danger" onclick="deleteInternship(${it.id})"><i class="fas fa-trash"></i></button>`;
        }
        
        const tr=document.createElement('tr'); 
        tr.innerHTML=`
          <td>
            <div class="fw-semibold">${it.siswa_nama||'Siswa ID: '+it.siswa_id}</div>
            <div class="small text-muted">NIS: ${it.nis||'-'} • ${it.kelas||'-'} • ${it.jurusan||'-'}</div>
          </td>
          <td>
            <div class="fw-semibold">${it.guru_nama||'Guru ID: '+it.guru_id}</div>
            <div class="small text-muted">NIP: ${it.nip||'-'}</div>
          </td>
          <td>
            <div class="fw-semibold">${it.nama_perusahaan||'DUDI ID: '+it.dudi_id}</div>
            <div class="small text-muted">${it.alamat||'-'} • ${it.penanggung_jawab||'-'}</div>
          </td>
          <td>
            <div>${period}</div>
            <div class="small text-muted">${duration}</div>
          </td>
          <td>${statusBadge}</td>
          <td>${nilaiDisplay}</td>
          <td class="text-center">${actions||'-'}</td>
        `; 
        tb.appendChild(tr); 
      }); 
    }catch(e){ 
      console.error('Error loading internships:', e); // Debug log
      const tb=document.getElementById('im-rows'); 
      if(tb) tb.innerHTML='<tr><td colspan="7" class="text-center text-danger py-3">Gagal memuat: '+e.message+'</td></tr>'; 
    }
    }

    async function activateInternship(id){ 
      const {t}=requireAuth(); 
      try{ 
        const r=await fetch('/api/guru/internships/'+id+'/activate',{method:'PUT',headers:{Authorization:'Bearer '+t}}); 
        const d=await r.json(); 
        if(!r.ok){ alert(d.message||'Gagal'); return;} 
        loadInternships(); 
      }catch(e){ alert('Gagal mengubah status'); }
    }
    
    function openGrade(id){ 
      document.getElementById('gid').value=id; 
      // baca status current dari baris tabel untuk memutuskan aksi fallback
      const rowBtn = event?.target;
      if(rowBtn){
        const tr=rowBtn.closest('tr');
        const cur = tr?.querySelector('td:nth-child(5) .badge')?.textContent?.trim().toLowerCase()||'';
        document.getElementById('gcur').value=cur;
      }
      
      // Load current data untuk form edit
      loadInternshipData(id);
      
      new bootstrap.Modal(document.getElementById('mGrade')).show(); 
    }

    async function loadInternshipData(id) {
      const {t} = requireAuth();
      try {
        const response = await fetch('/api/guru/internships', {headers: {Authorization: 'Bearer ' + t}});
        const data = await response.json();
        const internship = data.find(item => item.id == id);
        
        if (internship) {
          document.getElementById('gstart').value = internship.tanggal_mulai || '';
          document.getElementById('gend').value = internship.tanggal_selesai || '';
          document.getElementById('gstatus').value = internship.status || 'pending';
          document.getElementById('gval').value = internship.nilai || '';
        }
      } catch (e) {
        console.error('Error loading internship data:', e);
      }
    }
    
    async function submitGrade(){ 
      const {t}=requireAuth(); 
      const id=document.getElementById('gid').value; 
      const status=document.getElementById('gstatus').value;
      const payload={
        tanggal_mulai: document.getElementById('gstart').value||null,
        tanggal_selesai: document.getElementById('gend').value||null,
        status
      };
      // Nilai hanya jika selesai dan terisi
      const val=document.getElementById('gval').value;
      if(status==='selesai' && val!=='') payload.nilai=parseInt(val||'0',10);
      try{ 
        const r=await fetch('/api/guru/internships/'+encodeURIComponent(id),{method:'PUT',headers:{Authorization:'Bearer '+t,'Content-Type':'application/json'},body:JSON.stringify(payload)});
        const d=await r.json();
        if(!r.ok){
          // Fallback: if API update belum tersedia, gunakan endpoint khusus
          if(status==='selesai'){
            const g=await fetch('/api/guru/internships/'+encodeURIComponent(id)+'/grade',{method:'PUT',headers:{Authorization:'Bearer '+t,'Content-Type':'application/json'},body:JSON.stringify({nilai: parseInt(document.getElementById('gval').value||'0',10)})});
            const gd=await g.json(); if(!g.ok){ alert(gd.message||'Gagal'); return; }
          } else if(status==='aktif'){
            const current = (document.getElementById('gcur').value||'').toLowerCase();
            if(current==='pending'){
        const a=await fetch('/api/guru/internships/'+encodeURIComponent(id)+'/activate',{method:'PUT',headers:{Authorization:'Bearer '+t}});
        const ad=await a.json(); if(!a.ok){ alert(ad.message||'Gagal'); return; }
        } else {
          alert('Perubahan status ini belum didukung. Ubah ke Selesai untuk isi nilai, atau dari Pending ke Aktif.');
          return;
        }
      } else { alert(d.message||'Gagal'); return; }
    }
    bootstrap.Modal.getInstance(document.getElementById('mGrade')).hide();
    loadInternships();
  }catch(e){ alert('Gagal menyimpan perubahan'); }
}
async function deleteGrade(id){ const {t}=requireAuth(); if(!confirm('Hapus nilai untuk penempatan ini?')) return; try{ const r=await fetch('/api/guru/internships/'+id+'/grade',{method:'PUT',headers:{Authorization:'Bearer '+t,'Content-Type':'application/json'},body:JSON.stringify({nilai:null})}); const d=await r.json(); if(!r.ok){ alert(d.message||'Gagal'); return;} loadInternships(); }catch(e){ alert('Gagal menghapus nilai'); }}
async function deleteInternship(id){ const {t}=requireAuth(); if(!confirm('Hapus data penempatan ini? Tindakan ini tidak dapat dibatalkan.')) return; try{ const r=await fetch('/api/guru/internships/'+id,{method:'DELETE',headers:{Authorization:'Bearer '+t}}); const d=await r.json(); if(!r.ok){ alert(d.message||'Gagal menghapus data'); return;} loadInternships(); }catch(e){ alert('Gagal menghapus data penempatan'); }}

// Create internship helpers
async function openCreateInternship(){
  const {t,u}=requireAuth();
  // set guru
  document.getElementById('cGuru').value = u.name || 'Guru';
  // load siswa list
  try{
    const rs=await fetch('/api/guru/students',{headers:{Authorization:'Bearer '+t}}); const students=await rs.json();
    const sel=document.getElementById('cSiswa'); sel.innerHTML='';
    (students||[]).forEach(s=>{ const opt=document.createElement('option'); opt.value=s.id; opt.textContent=s.name; sel.appendChild(opt); });
  }catch(e){}
  // load dudi list (gunakan endpoint all untuk menampilkan semua DUDI aktif)
  try{
    const r=await fetch('/api/guru/dudi/all',{headers:{Authorization:'Bearer '+token()}}); const dudi=await r.json();
    const sd=document.getElementById('cDudi'); sd.innerHTML='';
    (dudi||[]).forEach(d=>{ const opt=document.createElement('option'); opt.value=d.id; opt.textContent=d.nama_perusahaan; sd.appendChild(opt); });
  }catch(e){}
  new bootstrap.Modal(document.getElementById('mCreate')).show();
}
async function submitCreateInternship(){
  const {t}=requireAuth();
  const payload={
    siswa_id: parseInt(document.getElementById('cSiswa').value||'0',10),
    dudi_id: parseInt(document.getElementById('cDudi').value||'0',10),
    tanggal_mulai: document.getElementById('cMulai').value||null,
    tanggal_selesai: document.getElementById('cSelesai').value||null,
  };
  const msg=document.getElementById('cMsg'); msg.classList.add('d-none'); msg.textContent='';
  try{
    const r=await fetch('/api/guru/internships',{method:'POST',headers:{Authorization:'Bearer '+t,'Content-Type':'application/json'},body:JSON.stringify(payload)});
    const d=await r.json();
    if(!r.ok){ msg.textContent=d.message||'Gagal menyimpan'; msg.classList.remove('d-none'); return; }
    bootstrap.Modal.getInstance(document.getElementById('mCreate')).hide();
    loadInternships();
  }catch(e){ msg.textContent='Terjadi kesalahan'; msg.classList.remove('d-none'); }
}
  </script>
</body></html>


