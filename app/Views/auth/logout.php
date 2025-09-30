<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Logout...</title>
  <script>
    document.addEventListener('DOMContentLoaded',()=>{
      try { localStorage.clear(); } catch(e) {}
      location.replace('/login');
    });
  </script>
</head>
<body>
  <p>Logging out...</p>
</body>
</html>


