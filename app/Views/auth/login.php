<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login SIMMAS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { min-height:100vh; background: radial-gradient(1200px 600px at 80% -10%, #f0f6ff 40%, transparent 41%), radial-gradient(1200px 600px at -10% 100%, #f5f7fb 40%, transparent 41%), linear-gradient(135deg, #f6f9ff 0%, #ffffff 60%); }
    .card { box-shadow:0 14px 40px rgba(31, 80, 219, .08); border:0; border-radius:16px; }
    .avatar { height:56px; width:56px; border-radius:50%; display:inline-flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#3b82f6,#1d4ed8); color:#fff; font-size:26px; box-shadow:0 8px 20px rgba(29,78,216,.25); }
    .brand { letter-spacing:.3px; }
    .form-control, .input-group-text { border-radius:12px; }
    .input-group-text { background:#f2f4f8; }
    .toggle-eye { cursor:pointer; }
  </style>
  <script>
    async function handleLogin(e){
      e.preventDefault();
      const form = e.target;
      const email = form.email.value.trim();
      const password = form.password.value;
      const btn = form.querySelector('button[type=submit]');
      btn.disabled = true; btn.innerText = 'Masuk...';
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
      } finally { btn.disabled=false; btn.innerText='Masuk'; }
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
          eye.innerHTML = shown ? '👁️' : '🙈';
        });
      }
    });
  </script>
</head>
<body class="d-flex align-items-center">
  <div class="container py-4">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <div class="text-center mb-4">
          <div class="avatar mb-2">👤</div>
          <h3 class="fw-bold brand mb-1">Welcome Back</h3>
          <div class="text-muted">Masuk ke akun SIMMAS Anda</div>
        </div>
        <div class="card p-4">
          <form onsubmit="handleLogin(event)">
            <div id="msg" class="alert alert-danger py-2 small mb-3 d-none" role="alert"></div>
            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <div class="input-group">
                <span class="input-group-text">📧</span>
                <input name="email" type="email" class="form-control" placeholder="admin@simmas.test" autocomplete="username" required>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <div class="input-group">
                <span class="input-group-text">🔒</span>
                <input id="password" name="password" type="password" class="form-control" value="password" autocomplete="current-password" required>
                <span id="toggle-eye" class="input-group-text toggle-eye" title="Tampilkan/Sembunyikan">👁️</span>
              </div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Sign In</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


