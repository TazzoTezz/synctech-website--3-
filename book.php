<?php
$sent = false;
$error = false;
$recaptcha_error = false;
$webhook_url = 'https://discord.com/api/webhooks/1378918337060536340/7GzufMWwFhU4Nekjyc_MRTee1a9sq8S9AJ2lA8SYyEKze4X9oLzRXaKbAW702gA38DFf';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = htmlspecialchars(trim($_POST['name'] ?? ''));
  $email = htmlspecialchars(trim($_POST['email'] ?? ''));
  $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
  $issue = htmlspecialchars(trim($_POST['issue'] ?? ''));
  $datetime = htmlspecialchars(trim($_POST['datetime'] ?? ''));
  $recaptcha_token = $_POST['recaptcha_token'] ?? '';

  // Verify reCAPTCHA v3
  $secret = '6Lcp1lIrAAAAAOYCLHeRKW20zjDoT8bJK-E9SkIa';
  $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = [
    'secret' => $secret,
    'response' => $recaptcha_token,
    'remoteip' => $_SERVER['REMOTE_ADDR']
  ];
  $options = [
    'http' => [
      'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data),
      'timeout' => 5
    ]
  ];
  $context  = stream_context_create($options);
  $verify = @file_get_contents($recaptcha_url, false, $context);
  $captcha_success = json_decode($verify);

  if ($name && $email && $issue && !empty($captcha_success->success) && $captcha_success->success === true && $captcha_success->score >= 0.5) {
    // Send to Discord webhook
    $discord_data = [
      "embeds" => [[
        "title" => "New Booking Request",
        "color" => 65280,
        "fields" => [
          ["name" => "Name", "value" => $name, "inline" => true],
          ["name" => "Email", "value" => $email, "inline" => true],
          ["name" => "Phone", "value" => $phone ?: 'N/A', "inline" => true],
          ["name" => "Preferred Date & Time", "value" => $datetime ?: 'N/A', "inline" => false],
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
    if (empty($captcha_success->success) || $captcha_success->success !== true || $captcha_success->score < 0.5) {
      $recaptcha_error = true;
    } else {
      $error = true;
    }
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
  <script src="https://www.google.com/recaptcha/api.js?render=6Lcp1lIrAAAAAJFnn57bf4cDDybUkVb0BDoHoMrD"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var form = document.getElementById('book-form');
      if (form) {
        form.addEventListener('submit', function(e) {
          e.preventDefault();
          grecaptcha.ready(function() {
            grecaptcha.execute('6Lcp1lIrAAAAAJFnn57bf4cDDybUkVb0BDoHoMrD', {action: 'book'}).then(function(token) {
              document.getElementById('recaptcha_token').value = token;
              form.submit();
            });
          });
        });
      }
    });
  </script>
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
    <div style="display: flex; flex-wrap: wrap; gap: 48px; justify-content: center;">
      <div style="flex:2; min-width:320px; max-width:480px; background:#181a1b; border-radius:18px; box-shadow:0 2px 18px rgba(0,255,206,0.07); padding:40px 28px;">
        <form id="book-form" method="POST" autocomplete="off">
          <div style="margin-bottom: 26px;">
            <label for="book-name" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
              <span style="margin-right:8px;">&#128100;</span> Name
            </label>
            <input type="text" id="book-name" name="name" required style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem;">
          </div>
          <div style="margin-bottom: 26px;">
            <label for="book-email" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
              <span style="margin-right:8px;">&#9993;</span> Email
            </label>
            <input type="email" id="book-email" name="email" required style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem;">
          </div>
          <div style="margin-bottom: 26px;">
            <label for="book-phone" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
              <span style="margin-right:8px;">&#128222;</span> Phone
            </label>
            <input type="tel" id="book-phone" name="phone" style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem;">
          </div>
          <div style="margin-bottom: 26px;">
            <label for="book-issue" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
              <span style="margin-right:8px;">&#9881;</span> What do you need help with?
            </label>
            <textarea id="book-issue" name="issue" rows="5" required style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem; resize:vertical;"></textarea>
          </div>
          <div style="margin-bottom: 32px;">
            <label for="book-datetime" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
              <span style="margin-right:8px;">&#128197;</span> Preferred Date & Time
            </label>
            <input type="datetime-local" id="book-datetime" name="datetime" style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem;">
          </div>
          <input type="hidden" name="recaptcha_token" id="recaptcha_token">
          <button type="submit" class="btn-primary" style="width:100%; font-size:1.15rem;">Book Now</button>
        </form>
        <?php if ($sent): ?>
          <div style="background:#181a1b; color:#00ffce; border-radius:8px; padding:24px; text-align:center; margin-top:24px;">
            <b>Thank you!</b> One of our Wellington based team will be in contact shortly.
          </div>
        <?php elseif ($recaptcha_error): ?>
          <div style="background:#181a1b; color:#ff5c5c; border-radius:8px; padding:24px; text-align:center; margin-top:24px;">
            Please complete the form again. reCAPTCHA verification failed.
          </div>
        <?php elseif ($error && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
          <div style="background:#181a1b; color:#ff5c5c; border-radius:8px; padding:24px; text-align:center; margin-top:24px;">
            Sorry, there was a problem sending your request. Please try again or email <a href="mailto:help@synctech.co.nz" style="color:#00ffce;">help@synctech.co.nz</a>.
          </div>
        <?php endif; ?>
      </div>
      <div style="flex:1; min-width:260px; max-width:340px; background:#161a1d; border-radius:14px; box-shadow:0 2px 12px rgba(0,255,206,0.04); padding:36px 22px; color:#b0b0b0; display:flex; flex-direction:column; justify-content:center;">
        <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=400&q=80" alt="IT Technician with PC" style="width:100%; border-radius:10px; margin-bottom:18px; object-fit:cover; height:120px;">
        <h3 style="color:#00ffce; margin-bottom:18px;">How Booking Works</h3>
        <ul style="list-style:none; padding:0; font-size:1.01rem; margin-bottom:18px;">
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