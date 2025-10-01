<?php
// Get user data from passed data or session
$user = $user ?? session('user') ?? null;
$userRole = $user['role'] ?? 'guest';

// Load school info from database
$db = db_connect();
$schoolInfo = $db->table('school_settings')->get()->getRowArray();
$schoolName = $schoolInfo['nama_sekolah'] ?? 'SMK Negeri 1 Surabaya';

// Debug logging
log_message('debug', 'Sidebar school name loaded: ' . $schoolName);
$appVersion = 'v1.0';

// Role-based menu items
$menuItems = match($userRole) {
    'admin' => [
        ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'desc' => 'Ringkasan sistem', 'link' => '#dashboard', 'active' => true],
        ['icon' => 'fas fa-industry', 'text' => 'DUDI', 'desc' => 'Manajemen DUDI', 'link' => '#dudi', 'active' => false],
        ['icon' => 'fas fa-users', 'text' => 'Pengguna', 'desc' => 'Manajemen user', 'link' => '#users', 'active' => false],
        ['icon' => 'fas fa-cog', 'text' => 'Pengaturan', 'desc' => 'Konfigurasi sistem', 'link' => '#settings', 'active' => false],
    ],
    'guru' => [
        ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'desc' => 'Ringkasan sistem', 'link' => '#dashboard', 'active' => true],
        ['icon' => 'fas fa-industry', 'text' => 'DUDI', 'desc' => 'Manajemen DUDI', 'link' => '#dudi', 'active' => false],
        ['icon' => 'fas fa-briefcase', 'text' => 'Magang', 'desc' => 'Manajemen magang', 'link' => '#magang', 'active' => false],
        ['icon' => 'fas fa-book', 'text' => 'Jurnal Harian', 'desc' => 'Verifikasi logbook', 'link' => '#logbook', 'active' => false],
    ],
    'siswa' => [
        ['icon' => 'fas fa-tachometer-alt', 'text' => 'Dashboard', 'desc' => 'Ringkasan sistem', 'link' => '#dashboard', 'active' => true],
        ['icon' => 'fas fa-industry', 'text' => 'DUDI', 'desc' => 'Daftar magang', 'link' => '#dudi', 'active' => false],
        ['icon' => 'fas fa-book', 'text' => 'Jurnal Harian', 'desc' => 'Logbook harian', 'link' => '#logbook', 'active' => false],
        ['icon' => 'fas fa-briefcase', 'text' => 'Magang', 'desc' => 'Status magang', 'link' => '#magang', 'active' => false],
    ],
    default => []
};
?>

<!-- Sidebar Component -->
<aside id="sidebar" class="sidebar d-flex flex-column" style="min-height: 100vh; width: 300px; transition: transform 0.3s ease;">
  <!-- Sidebar Header -->
  <div class="sidebar-header">
    <div class="d-flex align-items-center justify-content-between w-100">
      <div class="d-flex align-items-center flex-grow-1">
        <div class="sidebar-logo me-3">
          <div class="logo-icon">
            <i class="fas fa-graduation-cap"></i>
          </div>
        </div>
        <div>
          <h5 class="mb-0 fw-bold text-dark sidebar-title">SIMMAS</h5>
          <small class="text-muted sidebar-subtitle"><?= ucfirst($userRole) ?> Panel</small>
        </div>
      </div>
      <!-- Close Button -->
      <button class="btn btn-link text-muted p-1 d-lg-none ms-auto sidebar-close" type="button" onclick="closeSidebar()" aria-label="Close sidebar">
        <i class="fas fa-times fs-5"></i>
      </button>
    </div>
  </div>

  <!-- Navigation Menu -->
  <nav class="p-3 flex-grow-1">
    <h6 class="text-muted mb-3 fw-semibold" id="sidebar-menu-label">Menu</h6>
    <ul class="nav nav-pills flex-column gap-3">
      <?php foreach ($menuItems as $item): ?>
      <li class="nav-item">
        <a class="nav-link sidebar-link d-flex align-items-center py-3 px-4 rounded-3 text-decoration-none <?= $item['active'] ? 'active' : '' ?>" 
           href="<?= $item['link'] ?>" 
           data-link="<?= $item['text'] === 'Dashboard' ? 'sec-dashboard' : ($item['text'] === 'DUDI' ? 'sec-dudi' : ($item['text'] === 'Jurnal Harian' ? 'sec-logbook' : ($item['text'] === 'Magang' ? 'sec-magang' : ($item['text'] === 'Pengguna' ? 'sec-users' : ($item['text'] === 'Pengaturan' ? 'sec-settings' : 'sec-dashboard'))))) ?>"
           onclick="console.log('Menu clicked:', '<?= $item['text'] ?>'); if(typeof handleMenuClick === 'function') { handleMenuClick('<?= $item['text'] === 'Dashboard' ? 'sec-dashboard' : ($item['text'] === 'DUDI' ? 'sec-dudi' : ($item['text'] === 'Jurnal Harian' ? 'sec-logbook' : ($item['text'] === 'Magang' ? 'sec-magang' : ($item['text'] === 'Pengguna' ? 'sec-users' : ($item['text'] === 'Pengaturan' ? 'sec-settings' : 'sec-dashboard'))))) ?>'); } else { console.error('handleMenuClick function not found'); }">
          <div class="d-flex align-items-center w-100">
            <i class="<?= $item['icon'] ?> me-3 fs-4"></i>
            <div class="flex-grow-1">
              <div class="fw-semibold mb-0"><?= $item['text'] ?></div>
              <small class="opacity-75"><?= $item['desc'] ?></small>
            </div>
          </div>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <!-- Sidebar Footer -->
  <div class="sidebar-footer">
    <div class="d-flex align-items-center mb-1">
      <div class="status-indicator me-2"></div>
      <small class="text-muted fw-semibold mb-0"><?= $schoolName ?></small>
    </div>
    <div class="small text-muted">
      Sistem Pelaporan <?= $appVersion ?>
    </div>
  </div>
</aside>

<style>
/* Sidebar header styling to match navbar height */
.sidebar-header {
  padding: 0.75rem 1.5rem;
  height: 80px;
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.8);
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  backdrop-filter: blur(10px);
}

.sidebar-logo .logo-icon {
  width: 40px;
  height: 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.1rem;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  transition: all 0.3s ease;
}

.sidebar-title {
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a202c;
  letter-spacing: -0.025em;
  margin-bottom: 0.1rem;
}

.sidebar-subtitle {
  font-size: 0.75rem;
  font-weight: 500;
  color: #718096;
}

.sidebar-close {
  border-radius: 8px;
  padding: 0.5rem;
  transition: all 0.3s ease;
  background: rgba(0, 0, 0, 0.05);
}

.sidebar-close:hover {
  background: rgba(0, 0, 0, 0.1);
  transform: scale(1.05);
}

.sidebar-footer {
  padding: 1rem 1.5rem;
  background: rgba(248, 250, 252, 0.8);
  border-top: 1px solid rgba(0, 0, 0, 0.08);
  backdrop-filter: blur(10px);
}

.status-indicator {
  width: 8px;
  height: 8px;
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 50%;
  box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

/* Responsive adjustments for sidebar */
@media (max-width: 768px) {
  .sidebar-header {
    padding: 0.75rem 1rem;
    height: 80px;
  }
  
  .sidebar-footer {
    padding: 1rem;
  }
  
  .sidebar-title {
    font-size: 1rem;
  }
  
  .sidebar-subtitle {
    font-size: 0.7rem;
  }
  
  .sidebar-logo .logo-icon {
    width: 36px;
    height: 36px;
    font-size: 1rem;
  }
}

@media (max-width: 576px) {
  .sidebar {
    width: 260px;
  }
  
  .sidebar-header {
    padding: 0.5rem 0.75rem;
  }
  
  .sidebar-footer {
    padding: 0.75rem;
  }
  
  .sidebar .nav-link {
    padding: 1rem 1.25rem;
    min-height: 3.5rem;
  }
  
  .sidebar .nav-link .flex-grow-1 small {
    display: none;
  }
}
</style>

<style>
/* Sidebar styles */
.sidebar {
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
  border-right: 1px solid rgba(0, 0, 0, 0.08);
  backdrop-filter: blur(20px);
}

.sidebar .nav-link {
  color: #64748b;
  transition: all 0.3s ease;
  border: none;
  background: none;
  border-radius: 12px;
  margin-bottom: 0.5rem;
  position: relative;
  overflow: hidden;
  padding: 1rem 1.25rem;
  min-height: 4rem;
  display: flex;
  align-items: center;
}

.sidebar .nav-link:hover {
  background: rgba(102, 126, 234, 0.1);
  color: #475569;
  transform: translateX(4px);
}

.sidebar .nav-link.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  transform: translateX(4px);
}

.sidebar .nav-link.active:hover {
  background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
  color: white;
  transform: translateX(4px);
}

.sidebar .nav-link.active i {
  color: white;
}

.sidebar .nav-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.sidebar .nav-link:hover::before {
  left: 100%;
}

.sidebar .nav-link i {
  width: 24px;
  text-align: center;
  flex-shrink: 0;
  font-size: 1.1rem;
}

.sidebar .nav-link .flex-grow-1 {
  display: flex;
  flex-direction: column;
  justify-content: center;
  line-height: 1.2;
}

.sidebar .nav-link .fw-semibold {
  font-size: 1.1rem;
  line-height: 1.3;
}

.sidebar .nav-link small {
  font-size: 0.9rem;
  line-height: 1.3;
  margin-top: 0.3rem;
}

/* Sidebar toggle styles */
.sidebar.collapsed {
  transform: translateX(-100%);
  position: fixed;
  z-index: 1050;
}

.sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1040;
  display: none;
}

.sidebar-overlay.show {
  display: block;
}

/* Hide overlay on desktop */
@media (min-width: 769px) {
  .sidebar-overlay {
    display: none !important;
  }
}

/* Show overlay on mobile */
@media (max-width: 768px) {
  .sidebar-overlay {
    display: block;
  }
  
  .sidebar-overlay:not(.show) {
    display: none;
  }
}

/* Responsive sidebar */
@media (max-width: 768px) {
  .sidebar {
    position: fixed;
    top: 0;
    left: 0;
    z-index: 1050;
    transform: translateX(-100%);
  }
  
  .sidebar:not(.collapsed) {
    transform: translateX(0);
  }
  
  .sidebar.collapsed {
    transform: translateX(-100%);
  }
  
  .sidebar .nav-link {
    padding: 1rem 1.25rem;
    min-height: 3.5rem;
  }
  
  .sidebar .nav-link .flex-grow-1 small {
    display: none;
  }
}

/* Desktop sidebar toggle */
@media (min-width: 769px) {
  .sidebar {
    position: relative;
  }
  
  .sidebar.collapsed {
    transform: translateX(-100%);
    position: fixed;
    z-index: 1050;
  }
}

/* Animation for active state */
.sidebar .nav-link {
  position: relative;
  overflow: hidden;
}

.sidebar .nav-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: left 0.5s;
}

.sidebar .nav-link:hover::before {
  left: 100%;
}
</style>

<script>
// Sidebar toggle functionality
function toggleSidebar() {
  console.log('toggleSidebar() called');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  
  console.log('Sidebar element:', sidebar);
  console.log('Overlay element:', overlay);
  console.log('Window width:', window.innerWidth);
  
  if (sidebar) {
    console.log('Current sidebar classes:', sidebar.className);
    sidebar.classList.toggle('collapsed');
    console.log('New sidebar classes:', sidebar.className);
    
    // Show/hide overlay ONLY on mobile
    if (window.innerWidth <= 768) {
      if (overlay) {
        overlay.classList.toggle('show');
        console.log('Overlay toggled for mobile');
      }
    } else {
      // Desktop: ensure overlay is hidden
      if (overlay) {
        overlay.classList.remove('show');
        console.log('Overlay hidden for desktop');
      }
    }
  } else {
    console.error('Sidebar element not found!');
  }
}

// Close sidebar when clicking overlay or close button
function closeSidebar() {
  console.log('closeSidebar() called');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  
  if (sidebar) {
    sidebar.classList.add('collapsed');
    console.log('Sidebar closed');
    
    if (overlay) {
      overlay.classList.remove('show');
      console.log('Overlay hidden');
    }
  }
}

// Handle window resize
window.addEventListener('resize', function() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  
  if (window.innerWidth > 768) {
    // Desktop: hide overlay
    if (overlay) {
      overlay.classList.remove('show');
    }
  }
});

// Initialize sidebar state
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  
  console.log('Initializing sidebar...');
  console.log('Window width:', window.innerWidth);
  
  // On mobile, start with sidebar closed
  if (window.innerWidth <= 768) {
    console.log('Mobile detected - starting with sidebar collapsed');
    if (sidebar) {
      sidebar.classList.add('collapsed');
      console.log('Sidebar classes after init:', sidebar.className);
    }
  } else {
    console.log('Desktop detected - ensuring overlay is hidden');
    // Desktop: ensure overlay is hidden
    if (overlay) {
      overlay.classList.remove('show');
    }
  }
  
  // Prevent menu clicks from affecting sidebar state
  const menuLinks = document.querySelectorAll('.sidebar-link');
  menuLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      // Don't let menu clicks affect sidebar state
      e.stopPropagation();
      e.stopImmediatePropagation();
      
      // Debug: log the click
      console.log('Sidebar link clicked:', this.textContent.trim());
      
      // Allow onclick to execute
      const onclickAttr = this.getAttribute('onclick');
      if (onclickAttr) {
        console.log('Executing onclick:', onclickAttr);
        eval(onclickAttr);
      }
    });
    
    link.addEventListener('mousedown', function(e) {
      e.stopPropagation();
    });
  });
  
  // Override any global click handlers that might affect sidebar
  document.addEventListener('click', function(e) {
    // If click is on sidebar menu, prevent any sidebar toggle but allow onclick
    if (e.target.closest('.sidebar-link')) {
      console.log('Global click handler: sidebar link clicked');
      // Don't prevent the onclick from executing
      return;
    }
    
    // If click is on toggle button, allow it to work
    if (e.target.closest('[onclick*="toggleSidebar"]') || e.target.closest('[onclick*="closeSidebar"]')) {
      // Allow toggle button to work normally
      console.log('Global click handler: toggle button clicked');
      return;
    }
  }, true); // Use capture phase to intercept before other handlers
});
</script>
