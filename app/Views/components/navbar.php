<?php
// Get user data from passed data or session
$user = $user ?? session('user') ?? null;
$userRole = $user['role'] ?? 'guest';
$userName = $user['name'] ?? 'User';

// Load school info from database
$db = db_connect();
$schoolInfo = $db->table('school_settings')->get()->getRowArray();
$schoolName = $schoolInfo['nama_sekolah'] ?? 'SMK Negeri 1 Surabaya';

// Debug logging
log_message('debug', 'Navbar school name loaded: ' . $schoolName);
$appName = 'Sistem Manajemen Magang Siswa';
$appVersion = 'v1.0';

// Role-based panel text
$panelText = match($userRole) {
    'admin' => 'Panel Admin',
    'guru' => 'Panel Guru',
    'siswa' => 'Panel Siswa',
    default => 'Panel User'
};

// Role-based avatar color
$avatarColor = match($userRole) {
    'admin' => 'bg-primary',
    'guru' => 'bg-success', 
    'siswa' => 'bg-info',
    default => 'bg-secondary'
};
?>

<!-- Navbar Component -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm border-bottom">
  <div class="container-fluid" style="padding: 0.75rem 1.5rem; height: 80px; display: flex; align-items: center;">
    <!-- Left Side: Toggle Button and School Info -->
    <div class="d-flex align-items-center">
      <!-- Sidebar Toggle Button -->
      <button class="btn btn-link text-dark me-3 sidebar-toggle" type="button" onclick="toggleSidebar()" aria-label="Toggle sidebar">
        <i class="fas fa-bars fs-5"></i>
      </button>
      
      <!-- School Name -->
      <div class="d-flex align-items-center me-2">
        <div class="school-logo me-2">
          <div class="logo-icon">
            <i class="fas fa-graduation-cap"></i>
          </div>
        </div>
        <div>
          <h4 class="mb-0 fw-bold text-dark school-name"><?= $schoolName ?></h4>
          <small class="text-muted app-name"><?= $appName ?></small>
        </div>
      </div>
      
    </div>

    <!-- Right Side: User Info and Actions -->
    <div class="d-flex align-items-center">
      <!-- User Info -->
      <div class="d-flex align-items-center me-3">
        <!-- Avatar -->
        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 <?= $avatarColor ?>" style="width: 40px; height: 40px;">
          <i class="fas fa-user text-white"></i>
        </div>
        <!-- User Info -->
        <div class="text-start d-none d-md-block">
          <div class="fw-semibold text-dark mb-0" id="navbar-user-name"><?= $userName ?></div>
          <small class="text-muted text-capitalize" id="navbar-user-role"><?= $userRole ?></small>
        </div>
      </div>
      
      <!-- Logout Button -->
      <button class="btn btn-outline-danger btn-sm logout-btn" type="button" onclick="logout()" title="Logout">
        <i class="fas fa-sign-out-alt me-1"></i>
        <span class="d-none d-sm-inline">Logout</span>
      </button>
    </div>
  </div>
</nav>

<!-- Sidebar Overlay for Mobile -->
<div id="sidebar-overlay" class="sidebar-overlay" onclick="closeSidebar()"></div>

<style>
/* Custom styles for navbar */
.navbar {
  width: 100%;
  max-width: 100%;
  backdrop-filter: blur(10px);
  background: rgba(255, 255, 255, 0.95) !important;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08) !important;
}

.sidebar-toggle {
  border-radius: 12px;
  padding: 0.5rem;
  transition: all 0.3s ease;
  background: rgba(0, 0, 0, 0.05);
}

.sidebar-toggle:hover {
  background: rgba(0, 0, 0, 0.1);
  transform: scale(1.05);
}

.school-logo .logo-icon {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.25rem;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  transition: all 0.3s ease;
}

.school-logo .logo-icon:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}

.school-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a202c;
  letter-spacing: -0.025em;
}

.app-name {
  font-size: 0.8rem;
  font-weight: 500;
  color: #718096;
}

.logout-btn {
  border-radius: 12px;
  padding: 0.5rem 1rem;
  font-weight: 600;
  font-size: 0.875rem;
  transition: all 0.3s ease;
  border: 2px solid #dc2626;
  color: #dc2626;
  background: transparent;
}

.logout-btn:hover {
  background: #dc2626;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.logout-btn:active {
  transform: translateY(0);
}

.logout-btn i {
  font-size: 0.875rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .navbar .container-fluid {
    padding: 0.5rem 1rem !important;
    height: 80px !important;
  }
  
  .navbar .d-none.d-lg-block {
    display: none !important;
  }
  
  .navbar .d-none.d-md-block {
    display: none !important;
  }
  
  .school-name {
    font-size: 1.1rem;
  }
  
  .app-name {
    font-size: 0.75rem;
  }
  
  .school-logo .logo-icon {
    width: 40px;
    height: 40px;
    font-size: 1.1rem;
  }
  
  .logout-btn {
    padding: 0.4rem 0.75rem;
    font-size: 0.8rem;
  }
}

@media (max-width: 576px) {
  .navbar .container-fluid {
    padding: 0.4rem 0.75rem !important;
  }
  
  .school-name {
    font-size: 1rem;
  }
  
  .app-name {
    display: none;
  }
  
  .school-logo .logo-icon {
    width: 36px;
    height: 36px;
    font-size: 1rem;
  }
  
  .logout-btn {
    padding: 0.35rem 0.6rem;
    font-size: 0.75rem;
  }
  
  .logout-btn span {
    display: none !important;
  }
}

</style>

<script>
// Navbar functionality
function logout() {
  if (confirm('Apakah Anda yakin ingin logout?')) {
    // Clear local storage
    localStorage.removeItem('simmas_token');
    localStorage.removeItem('simmas_user');
    
    // Redirect to logout
    window.location.href = '/logout';
  }
}

// Load user info from localStorage
function loadUserInfo() {
  try {
    const userData = JSON.parse(localStorage.getItem('simmas_user') || '{}');
    console.log('Loading user info:', userData);
    
    if (userData.name) {
      // Update navbar user name
      const navbarName = document.getElementById('navbar-user-name');
      if (navbarName) navbarName.textContent = userData.name;
    }
    
    if (userData.role) {
      // Update navbar user role
      const navbarRole = document.getElementById('navbar-user-role');
      if (navbarRole) navbarRole.textContent = userData.role;
    }
  } catch (error) {
    console.error('Error loading user info:', error);
  }
}

// Initialize navbar
document.addEventListener('DOMContentLoaded', function() {
  console.log('Initializing navbar...');
  
  // Load user info from localStorage
  loadUserInfo();
  
  // Initialize Bootstrap tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
