<?php
session_start();
include 'db_connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No user found with that email.";
    }

    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — ImSata</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Mono:wght@300;400;500&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
  :root {
    --bg-void: #080b10;
    --bg-base: #0d1117;
    --bg-card: #111720;
    --bg-card2: #141c27;
    --bg-surface: #1a2333;
    --bg-hover: #1e2a3a;
    --accent-blue: #5865f2;
    --accent-cyan: #00d4ff;
    --accent-green: #23d18b;
    --accent-purple: #9b59b6;
    --accent-gold: #ffd700;
    --text-primary: #e8ecf0;
    --text-secondary: #8b9ab0;
    --text-muted: #4a5568;
    --border: rgba(255,255,255,0.06);
    --border-accent: rgba(88,101,242,0.4);
    --radius: 12px;
    --radius-lg: 20px;
  }
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  html { scroll-behavior: smooth; }
  body { font-family: 'DM Sans', sans-serif; background: var(--bg-void); color: var(--text-primary); overflow-x: hidden; line-height: 1.6; min-height: 100vh; display: flex; flex-direction: column; }
  #bg-canvas { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
  ::-webkit-scrollbar { width: 5px; }
  ::-webkit-scrollbar-track { background: var(--bg-void); }
  ::-webkit-scrollbar-thumb { background: var(--accent-blue); border-radius: 3px; }

  /* NAV */
  nav {
    position: fixed; top: 0; width: 100%; z-index: 1000;
    padding: 0 2rem; height: 64px;
    display: flex; align-items: center; justify-content: space-between;
    background: rgba(8,11,16,0.8); backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border); transition: all 0.3s;
  }
  nav.scrolled { background: rgba(8,11,16,0.97); border-bottom-color: var(--border-accent); }
  .nav-logo {
    font-family: 'Syne', sans-serif; font-weight: 800; font-size: 1.3rem;
    letter-spacing: -0.02em; display: flex; align-items: center; gap: 0.5rem;
    text-decoration: none; color: var(--text-primary);
  }
  .logo-icon {
    width: 32px; height: 32px; background: var(--accent-blue); border-radius: 8px;
    display: flex; align-items: center; justify-content: center; font-size: 1rem;
    box-shadow: 0 0 20px rgba(88,101,242,0.5);
  }
  .nav-links { display: flex; align-items: center; gap: 0.25rem; list-style: none; }
  .nav-links a {
    color: var(--text-secondary); text-decoration: none; padding: 0.4rem 0.9rem;
    border-radius: 8px; font-size: 0.875rem; font-weight: 500; transition: all 0.2s;
  }
  .nav-links a:hover { color: var(--text-primary); background: var(--bg-hover); }
  .nav-cta {
    background: var(--accent-blue) !important; color: #fff !important;
    font-weight: 600 !important; box-shadow: 0 0 20px rgba(88,101,242,0.3);
  }
  .nav-cta:hover { box-shadow: 0 0 32px rgba(88,101,242,0.6) !important; }
  .hamburger { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 4px; }
  .hamburger span { width: 24px; height: 2px; background: var(--text-secondary); border-radius: 2px; transition: all 0.3s; }

  /* MAIN */
  main { flex: 1; display: flex; align-items: center; justify-content: center; padding: 6rem 1.5rem 3rem; position: relative; z-index: 1; }

  /* AUTH CARD */
  .auth-card {
    width: 100%; max-width: 460px;
    background: var(--bg-card); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: 2.5rem;
    position: relative; overflow: hidden;
    animation: fadeUp 0.6s 0.1s both;
  }
  .auth-card::before {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(135deg, rgba(88,101,242,0.07) 0%, transparent 60%);
    pointer-events: none;
  }
  .auth-badge {
    display: inline-flex; align-items: center; gap: 0.5rem;
    background: rgba(88,101,242,0.12); border: 1px solid rgba(88,101,242,0.3);
    border-radius: 100px; padding: 0.3rem 0.9rem; font-size: 0.75rem;
    font-family: 'DM Mono', monospace; color: var(--accent-cyan); margin-bottom: 1.25rem;
  }
  .pulse-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--accent-green); box-shadow: 0 0 8px var(--accent-green); animation: pulse 2s infinite; }
  @keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:0.5; transform:scale(1.4); } }
  .auth-title {
    font-family: 'Syne', sans-serif; font-weight: 800;
    font-size: 1.9rem; line-height: 1.15; letter-spacing: -0.02em; margin-bottom: 0.4rem;
  }
  .gradient-text {
    background: linear-gradient(135deg, var(--accent-blue) 0%, var(--accent-cyan) 100%);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
  }
  .auth-sub { font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 2rem; font-weight: 300; }
  .form-group { margin-bottom: 1.1rem; }
  .form-label { display: block; font-size: 0.78rem; font-weight: 500; color: var(--text-secondary); margin-bottom: 0.4rem; font-family: 'DM Mono', monospace; letter-spacing: 0.03em; }
  .form-input {
    width: 100%; background: var(--bg-base); border: 1px solid var(--border);
    border-radius: 9px; padding: 0.8rem 1rem; color: var(--text-primary);
    font-family: 'DM Sans', sans-serif; font-size: 0.9rem; transition: all 0.2s; outline: none;
  }
  .form-input:focus { border-color: var(--accent-blue); box-shadow: 0 0 0 3px rgba(88,101,242,0.15); }
  .form-input::placeholder { color: var(--text-muted); }
  .form-submit {
    width: 100%; background: var(--accent-blue); color: #fff; border: none;
    border-radius: 10px; padding: 0.9rem; font-family: 'Syne', sans-serif;
    font-weight: 700; font-size: 0.95rem; cursor: pointer; transition: all 0.2s;
    letter-spacing: 0.02em; margin-top: 0.5rem;
  }
  .form-submit:hover { background: #4752c4; box-shadow: 0 0 30px rgba(88,101,242,0.4); transform: translateY(-1px); }
  .auth-footer { text-align: center; margin-top: 1.5rem; font-size: 0.875rem; color: var(--text-secondary); }
  .auth-footer a { color: var(--accent-cyan); text-decoration: none; font-weight: 500; transition: opacity 0.2s; }
  .auth-footer a:hover { opacity: 0.8; }
  .divider { height: 1px; background: var(--border); margin: 1.5rem 0; }
  .alert {
    padding: 0.75rem 1rem; border-radius: 9px; font-size: 0.875rem;
    margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem;
  }
  .alert-error { background: rgba(255,80,80,0.1); border: 1px solid rgba(255,80,80,0.3); color: #ff6b6b; }
  .alert-success { background: rgba(35,209,139,0.1); border: 1px solid rgba(35,209,139,0.3); color: var(--accent-green); }
  .alert-success a { color: var(--accent-cyan); text-decoration: none; }

  /* FOOTER */
  footer { border-top: 1px solid var(--border); padding: 1.5rem 2rem; text-align: center; color: var(--text-muted); font-size: 0.8rem; position: relative; z-index: 1; }
  footer a { color: var(--accent-blue); text-decoration: none; }

  @keyframes fadeUp { from { opacity:0; transform:translateY(24px); } to { opacity:1; transform:translateY(0); } }

  @media (max-width: 768px) {
    .nav-links { display: none; }
    .hamburger { display: flex; }
    .nav-links.open { display: flex; flex-direction: column; position: absolute; top: 64px; left: 0; right: 0; background: rgba(8,11,16,0.98); backdrop-filter: blur(20px); border-bottom: 1px solid var(--border); padding: 1rem; gap: 0.25rem; }
    .auth-card { padding: 1.75rem 1.25rem; }
  }
</style>
</head>
<body>

<canvas id="bg-canvas"></canvas>

<!-- NAV -->
<nav id="navbar">
  <a href="index.php" class="nav-logo">
    <div class="logo-icon">⚡</div>
    ImSata
  </a>
  <ul class="nav-links" id="navLinks">
    <li><a href="index.php#about">About</a></li>
    <li><a href="index.php#services">Services</a></li>
    <li><a href="index.php#portfolio">Work</a></li>
    <li><a href="index.php#pricing">Pricing</a></li>
    <li><a href="index.php#contact">Contact</a></li>
    <li><a href="register.php" class="nav-cta">Register</a></li>
  </ul>
  <div class="hamburger" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </div>
</nav>

<!-- MAIN -->
<main>
  <div class="auth-card">
    <div class="auth-badge"><div class="pulse-dot"></div> Secure Login</div>
    <h1 class="auth-title">Welcome <span class="gradient-text">Back</span></h1>
    <p class="auth-sub">Sign in to your ImSata account to continue.</p>

    <?php if ($error): ?>
      <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="alert alert-success">✓ <?= $success ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label class="form-label">email address</label>
        <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
      </div>
      <div class="form-group">
        <label class="form-label">password</label>
        <input type="password" name="password" class="form-input" placeholder="Your password" required>
      </div>
      <button type="submit" class="form-submit">Sign In →</button>
    </form>

    <div class="divider"></div>
    <p class="auth-footer">Don't have an account? <a href="register.php">Create one free</a></p>
  </div>
</main>

<!-- FOOTER -->
<footer>
  <span style="font-family:'Syne',sans-serif;font-weight:700;color:var(--text-secondary)">Vihandu Marasinghe</span>
  — Custom Discord Bots &amp; Web Development &nbsp;·&nbsp; © 2024 ImSata. All rights reserved.
</footer>

<script>
// CANVAS BACKGROUND
const canvas = document.getElementById('bg-canvas');
const ctx = canvas.getContext('2d');
let W, H, dots;
function resize() { W = canvas.width = window.innerWidth; H = canvas.height = window.innerHeight; initDots(); }
function initDots() {
  dots = Array.from({length: 75}, () => ({
    x: Math.random() * W, y: Math.random() * H,
    r: Math.random() * 1.5 + 0.3,
    vx: (Math.random() - 0.5) * 0.25, vy: (Math.random() - 0.5) * 0.25,
    a: Math.random() * 0.4 + 0.1
  }));
}
function drawBg() {
  ctx.clearRect(0, 0, W, H);
  ctx.strokeStyle = 'rgba(88,101,242,0.04)'; ctx.lineWidth = 1;
  const gs = 60;
  for (let x = 0; x < W; x += gs) { ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, H); ctx.stroke(); }
  for (let y = 0; y < H; y += gs) { ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(W, y); ctx.stroke(); }
  dots.forEach((d, i) => {
    d.x += d.vx; d.y += d.vy;
    if (d.x < 0) d.x = W; if (d.x > W) d.x = 0;
    if (d.y < 0) d.y = H; if (d.y > H) d.y = 0;
    ctx.beginPath(); ctx.arc(d.x, d.y, d.r, 0, Math.PI * 2);
    ctx.fillStyle = `rgba(88,101,242,${d.a})`; ctx.fill();
    for (let j = i + 1; j < dots.length; j++) {
      const d2 = dots[j], dx = d.x - d2.x, dy = d.y - d2.y, dist = Math.sqrt(dx*dx + dy*dy);
      if (dist < 100) {
        ctx.beginPath(); ctx.moveTo(d.x, d.y); ctx.lineTo(d2.x, d2.y);
        ctx.strokeStyle = `rgba(88,101,242,${0.06 * (1 - dist/100)})`; ctx.lineWidth = 0.5; ctx.stroke();
      }
    }
  });
  requestAnimationFrame(drawBg);
}
resize(); window.addEventListener('resize', resize); drawBg();

// NAV
window.addEventListener('scroll', () => { document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 20); });
function toggleMenu() { document.getElementById('navLinks').classList.toggle('open'); }
document.querySelectorAll('.nav-links a').forEach(a => { a.addEventListener('click', () => document.getElementById('navLinks').classList.remove('open')); });
</script>
</body>
</html>
