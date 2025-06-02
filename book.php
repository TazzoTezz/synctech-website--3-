<?php
$sent = false;
$error = false;
$webhook_url = 'https://discord.com/api/webhooks/1378918337060536340/7GzufMWwFhU4Nekjyc_MRTee1a9sq8S9AJ2lA8SYyEKze4X9oLzRXaKbAW702gA38DFf';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = htmlspecialchars(trim($_POST['name'] ?? ''));
  $email = htmlspecialchars(trim($_POST['email'] ?? ''));
  $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
  $issue = htmlspecialchars(trim($_POST['issue'] ?? ''));

  if ($name && $email && $issue) {
    $discord_data = [
      "embeds" => [[
        "title" => "New Booking Request",
        "color" => 65280,
        "fields" => [
          ["name" => "Name", "value" => $name, "inline" => true],
          ["name" => "Email", "value" => $email, "inline" => true],
          ["name" => "Phone", "value" => $phone ?: 'N/A', "inline" => true],
          ["name" => "Issue", "value" => $issue, "inline" => false]
        ],
        "footer" => ["text" => "Sync Tech Booking Form"]
      ]]
    ];
    $discord_options = [
      'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($discord_data),
        'timeout' => 5
      ]
    ];
    $discord_context = stream_context_create($discord_options);
    $discord_result = @file_get_contents($webhook_url, false, $discord_context);
    $sent = $discord_result !== false;
    if (!$sent) $error = true;
  } else {
    $error = true;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Book a Technician | Sync Tech</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta http-equiv="Content-Security-Policy" content="default-src 'self'; style-src 'self' https://fonts.googleapis.com 'unsafe-inline'; font-src https://fonts.gstatic.com; img-src 'self' data: https:; script-src 'self'; connect-src 'self';">
  <meta http-equiv="Referrer-Policy" content="strict-origin-when-cross-origin">
  <meta http-equiv="X-Content-Type-Options" content="nosniff">
  <meta http-equiv="X-Frame-Options" content="SAMEORIGIN">
  <meta http-equiv="Permissions-Policy" content="geolocation=(), microphone=(), camera=()">
  <link rel="stylesheet" href="css/style.css" />
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet" crossorigin="anonymous">
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
  <header class="header">
    <div class="container">
      <div class="logo">
        <img src="images/logo.png" alt="Sync Tech Logo" style="height:90px;">
      </div>
      <div class="nav-toggle"><span></span><span></span><span></span></div>
      <nav class="nav">
        <a href="index.php">Home</a>
        <a href="services.php">Services</a>
        <a href="about.php">About</a>
        <a href="book.php" class="active">Book</a>
        <a href="plans.php">Plans</a>
        <a href="testimonials.php">Testimonials</a>
        <a href="contact.php">Contact</a>
        <a href="https://helpdesk.synctech.co.nz" target="_blank" rel="noopener" class="btn-black" tabindex="0" style="margin-left:10px; font-size:1.01rem; padding:8px 18px;">Helpdesk Login</a>
      </nav>
    </div>
  </header>

  <main class="container" style="padding: 60px 0;">
    <h2>Book a Technician</h2>
    <?php if ($sent): ?>
      <div style="background:#181a1b; color:#00ffce; border-radius:8px; padding:24px; text-align:center; margin-bottom:32px;">
        <b>Thank you!</b> One of our Wellington based team will be in contact shortly.
      </div>
    <?php elseif ($error && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
      <div style="background:#181a1b; color:#ff5c5c; border-radius:8px; padding:24px; text-align:center; margin-bottom:32px;">
        Sorry, there was a problem sending your request. Please try again or email <a href="mailto:help@synctech.co.nz" style="color:#00ffce;">help@synctech.co.nz</a>.
      </div>
    <?php endif; ?>
    <p style="text-align: center; max-width: 700px; margin: 0 auto 40px;">
      Let us know what issue you're having, and we'll schedule a mobile visit or remote session as soon as possible.
    </p>
    <div class="form-flex" style="display: flex; flex-wrap: nowrap; gap: 48px; justify-content: center; align-items: flex-start;">
      <div style="flex:2; min-width:320px; max-width:480px; background:#181a1b; border-radius:18px; box-shadow:0 2px 18px rgba(0,255,206,0.07); padding:40px 28px;">
        <form id="book-form" action="" method="POST" autocomplete="off">
          <div class="form-group">
            <input type="text" name="name" required placeholder=" " />
            <label>Name</label>
          </div>
          <div class="form-group">
            <input type="email" name="email" required placeholder=" " />
            <label>Email</label>
          </div>
          <div class="form-group">
            <input type="tel" name="phone" required placeholder=" " />
            <label>Phone</label>
          </div>
          <div class="form-group">
            <textarea name="issue" rows="4" required placeholder=" "></textarea>
            <label>Describe your issue</label>
          </div>
          <div style="margin-bottom: 24px;">
            <div class="cf-turnstile" data-sitekey="0x4AAAAAABfuos2D1QOUdVwK"></div>
          </div>
          <button type="submit" class="btn-primary" style="width:100%; font-size:1.15rem;">Book Now</button>
        </form>
      </div>
      <div class="sidebar-card" style="flex:1; min-width:260px; max-width:340px; margin:0;">
        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=400&q=80" alt="IT Technician with PC" style="width:100%; border-radius:10px; margin-bottom:18px; object-fit:cover; height:120px;">
        <h3 style="color:#00ffce; margin-bottom:18px;">How Booking Works</h3>
        <ul>
          <li>✔ We confirm your booking by phone or email</li>
          <li>✔ Choose mobile or remote support</li>
          <li>✔ No fix, no fee guarantee</li>
          <li>✔ Same-day appointments often available</li>
          <li>✔ Friendly, qualified technicians</li>
        </ul>
        <div style="margin-top:18px;">
          <span style="color:#00ffce;">&#128222;</span> <b>Need urgent help?</b><br>
          <a href="tel:0800796233" style="color:#00ffce; font-weight:600;">0800 SYNCED <span style="font-size:0.95em;">(0800 796 233)</span></a>
        </div>
      </div>
    </div>
  </main>

  <footer class="footer">
    <div class="container">
      <p>&copy; 2025 Sync Tech. Your On-Call IT Team.</p>
    </div>
  </footer>

  <script src="js/script.js" defer></script>
</body>
</html>