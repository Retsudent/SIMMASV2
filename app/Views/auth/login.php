<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login SIMMAS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: 'Inter', sans-serif;
      min-height: 100vh;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      background-attachment: fixed;
      position: relative;
      overflow-x: hidden;
    }
    
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
      pointer-events: none;
      z-index: 1;
    }
    
    .login-container {
      position: relative;
      z-index: 2;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
    }
    
    .login-card {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 24px;
      padding: 3rem;
      box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 420px;
      position: relative;
      overflow: hidden;
    }
    
    .login-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
      animation: gradientShift 3s ease-in-out infinite;
    }
    
    @keyframes gradientShift {
      0%, 100% { background: linear-gradient(90deg, #667eea, #764ba2, #f093fb); }
      50% { background: linear-gradient(90deg, #f093fb, #667eea, #764ba2); }
    }
    
    .logo-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, #667eea, #764ba2);
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 2rem;
      box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
      position: relative;
      overflow: hidden;
    }
    
    .logo-icon::before {
      content: '';
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
      animation: shine 2s infinite;
    }
    
    @keyframes shine {
      0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
      100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    
    .logo-icon i {
      font-size: 2rem;
      color: white;
      z-index: 1;
    }
    
    .login-title {
      font-size: 2rem;
      font-weight: 700;
      color: white;
      text-align: center;
      margin-bottom: 0.5rem;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .login-subtitle {
      color: rgba(255, 255, 255, 0.8);
      text-align: center;
      margin-bottom: 2rem;
      font-size: 1rem;
    }
    
    .form-group {
      position: relative;
      margin-bottom: 1.5rem;
    }
    
    .form-label {
      color: rgba(255, 255, 255, 0.9);
      font-weight: 500;
      margin-bottom: 0.5rem;
      font-size: 0.9rem;
    }
    
    .input-group {
      position: relative;
    }
    
    .form-control {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      border-radius: 12px;
      padding: 0.75rem 1rem 0.75rem 3rem;
      color: white;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      background: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.4);
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
      color: white;
    }
    
    .form-control::placeholder {
      color: rgba(255, 255, 255, 0.6);
    }
    
    .input-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(255, 255, 255, 0.7);
      z-index: 3;
    }
    
    .toggle-eye {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: rgba(255, 255, 255, 0.7);
      cursor: pointer;
      z-index: 3;
      padding: 0.25rem;
      border-radius: 4px;
      transition: all 0.3s ease;
    }
    
    .toggle-eye:hover {
      color: white;
      background: rgba(255, 255, 255, 0.1);
    }
    
    .btn-login {
      background: linear-gradient(135deg, #667eea, #764ba2);
      border: none;
      border-radius: 12px;
      padding: 0.75rem 2rem;
      font-weight: 600;
      font-size: 1rem;
      color: white;
      width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
      background: linear-gradient(135deg, #5a6fd8, #6a4190);
    }
    
    .btn-login:disabled {
      opacity: 0.7;
      transform: none;
      cursor: not-allowed;
    }
    
    .alert {
      background: rgba(220, 53, 69, 0.1);
      border: 1px solid rgba(220, 53, 69, 0.3);
      color: #ff6b6b;
      border-radius: 8px;
      font-size: 0.9rem;
    }
    
    @media (max-width: 576px) {
      .login-card {
        padding: 2rem 1.5rem;
        margin: 1rem;
      }
      
      .login-title {
        font-size: 1.75rem;
      }
      
      .logo-icon {
        width: 70px;
        height: 70px;
      }
    }
  </style>
  <script>
    async function handleLogin(e){
      e.preventDefault();
      const form = e.target;
      const email = form.email.value.trim();
      const password = form.password.value;
      const btn = form.querySelector('button[type=submit]');
      const btnText = btn.querySelector('.btn-text');
      btn.disabled = true; 
      btnText.textContent = 'Masuk...';
      try {
        const res = await fetch('/api/auth/login', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({email,password})});
        const data = await res.json();
        if(!res.ok){ throw new Error(data.message||'Gagal login'); }
        localStorage.setItem('simmas_token', data.access_token);
        localStorage.setItem('simmas_user', JSON.stringify(data.user));
        const role = data.user.role;
        if(role==='admin') location.href='/dashboard/admin';
        else if(role==='guru') location.href='/dashboard/guru';
        else location.href='/dashboard/siswa';
      } catch(err){
        const msg = document.getElementById('msg');
        msg.innerText = err.message;
        msg.classList.remove('d-none');
      } finally { 
        btn.disabled=false; 
        btnText.textContent='Masuk'; 
      }
    }
  </script>
  <script>
    document.addEventListener('DOMContentLoaded',()=>{
      const t=localStorage.getItem('simmas_token');
      if(t){
        const u = JSON.parse(localStorage.getItem('simmas_user')||'{}');
        const role = u.role||'siswa';
        location.href = '/dashboard/'+role;
      }
      const eye = document.getElementById('toggle-eye');
      const pwd = document.getElementById('password');
      if(eye && pwd){
        eye.addEventListener('click',()=>{
          const shown = pwd.getAttribute('type')==='text';
          pwd.setAttribute('type', shown ? 'password' : 'text');
          eye.innerHTML = shown ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
      }
    });
  </script>
</head>
<body>
  <div class="login-container">
    <div class="login-card">
      <div class="text-center mb-4">
        <div class="logo-icon">
          <i class="fas fa-graduation-cap"></i>
        </div>
        <h1 class="login-title">Welcome Back</h1>
        <p class="login-subtitle">Masuk ke akun SIMMAS Anda</p>
      </div>
      
      <form onsubmit="handleLogin(event)">
        <div id="msg" class="alert alert-danger py-2 small mb-3 d-none" role="alert"></div>
        
        <div class="form-group">
          <label class="form-label">Email Address</label>
          <div class="input-group">
            <i class="fas fa-envelope input-icon"></i>
            <input name="email" type="email" class="form-control" placeholder="Enter your email" autocomplete="username" required>
          </div>
        </div>
        
        <div class="form-group">
          <label class="form-label">Password</label>
          <div class="input-group">
            <i class="fas fa-lock input-icon"></i>
            <input id="password" name="password" type="password" class="form-control" placeholder="Enter your password" autocomplete="current-password" required>
            <button id="toggle-eye" class="toggle-eye" type="button" title="Tampilkan/Sembunyikan">
              <i class="fas fa-eye"></i>
            </button>
          </div>
        </div>
        
        <button class="btn-login" type="submit">
          <span class="btn-text">Masuk</span>
        </button>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


