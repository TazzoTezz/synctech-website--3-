<?php
$sent = false;
$error = false;
$recaptcha_error = false;
$webhook_url = 'https://discord.com/api/webhooks/1378918142654414960/1ORBYHklL9Ums6fQ4V3x5rKpp6btmgVbC2G2P1U1nMbP__3V-bbMI6UU-WoDq0c58Zt1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = htmlspecialchars(trim($_POST['name'] ?? ''));
  $email = htmlspecialchars(trim($_POST['email'] ?? ''));
  $phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
  $message = htmlspecialchars(trim($_POST['message'] ?? ''));
  $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

  // Verify reCAPTCHA v2
  $secret = '6Lcp1lIrAAAAAOYCLHeRKW20zjDoT8bJK-E9SkIa';
  $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
  $data = [
    'secret' => $secret,
    'response' => $recaptcha_response,
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

  if ($name && $email && $message && !empty($captcha_success->success) && $captcha_success->success === true) {
    $discord_data = [
      "embeds" => [[
        "title" => "New Contact Form Submission",
        "color" => 65280,
        "fields" => [
          ["name" => "Name", "value" => $name, "inline" => true],
          ["name" => "Email", "value" => $email, "inline" => true],
          ["name" => "Phone", "value" => $phone ?: 'N/A', "inline" => true],
          ["name" => "Message", "value" => $message, "inline" => false]
        ],
        "footer" => ["text" => "Sync Tech Contact Form"]
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
    $recaptcha_error = true;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Contact Us | Sync Tech</title>
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
        <a href="book.php">Book</a>
        <a href="plans.php">Plans</a>
        <a href="testimonials.php">Testimonials</a>
        <a href="contact.php" class="active">Contact</a>
        <a href="https://helpdesk.synctech.co.nz" target="_blank" rel="noopener" class="btn-black" tabindex="0" style="margin-left:10px; font-size:1.01rem; padding:8px 18px;">Helpdesk Login</a>
      </nav>
    </div>
  </header>

  <main class="container" style="padding: 60px 0;">
    <h2>Contact Sync Tech</h2>

    <?php if (!empty($sent)): ?>
      <div style="background:#181a1b; color:#00ffce; border-radius:8px; padding:24px; text-align:center; margin-bottom:32px;">
        <b>Thank you!</b> One of our Wellington based team will be in contact shortly.
      </div>
    <?php elseif (!empty($recaptcha_error)): ?>
      <div style="background:#181a1b; color:#ff5c5c; border-radius:8px; padding:24px; text-align:center; margin-bottom:32px;">
        Please complete the reCAPTCHA to submit the form.
      </div>
    <?php elseif (!empty($error)): ?>
      <div style="background:#181a1b; color:#ff5c5c; border-radius:8px; padding:24px; text-align:center; margin-bottom:32px;">
        Sorry, there was a problem sending your message. Please try again or email <a href="mailto:help@synctech.co.nz" style="color:#00ffce;">help@synctech.co.nz</a>.
      </div>
    <?php endif; ?>

    <form id="contact-form" method="POST" autocomplete="off">
      <div style="margin-bottom: 26px;">
        <label for="contact-name" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
          <span style="margin-right:8px;">&#128100;</span> Name
        </label>
        <input type="text" id="contact-name" name="name" required style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem;">
      </div>
      <div style="margin-bottom: 26px;">
        <label for="contact-email" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
          <span style="margin-right:8px;">&#9993;</span> Email
        </label>
        <input type="email" id="contact-email" name="email" required style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem;">
      </div>
      <div style="margin-bottom: 26px;">
        <label for="contact-phone" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
          <span style="margin-right:8px;">&#128222;</span> Phone
        </label>
        <input type="tel" id="contact-phone" name="phone" style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem;">
      </div>
      <div style="margin-bottom: 32px;">
        <label for="contact-message" style="font-weight:500; color:#00ffce; display:block; margin-bottom:6px;">
          <span style="margin-right:8px;">&#9998;</span> Your Message
        </label>
        <textarea id="contact-message" name="message" rows="5" required style="width:100%; padding:16px; border-radius:8px; border:1.5px solid #23272a; background:#23272a; color:#e0e0e0; font-size:1rem; resize:vertical;"></textarea>
      </div>
      <div style="margin-bottom: 24px; display:flex; justify-content:center;">
        <div class="g-recaptcha" data-sitekey="6Lcp1lIrAAAAAJFnn57bf4cDDybUkVb0BDoHoMrD"></div>
      </div>
      <button type="submit" class="btn-primary" style="width:100%; font-size:1.15rem;">Send Message</button>
    </form>

    <?php
    // Output the sidebar info after the form (no HTML changes needed in the HTML file)
    echo <<<HTML
    <div class="sidebar-card" style="flex:1; min-width:260px; max-width:340px; margin:0;">
        <h3 style="color:#00ffce; margin-bottom:18px;">Contact Details</h3>
        <div style="margin-bottom:18px;">
            <div style="margin-bottom:10px;"><span style="color:#00ffce;">&#128222;</span> <a href="tel:0800796233" style="color:#00ffce; font-weight:600;">0800 SYNCED <span style="font-size:0.95em;">(0800 796 233)</span></a></div>
            <div style="margin-bottom:10px;"><span style="color:#00ffce;">&#9993;</span> <a href="mailto:help@synctech.co.nz" style="color:#00ffce;">help@synctech.co.nz</a></div>
            <div><span style="color:#00ffce;">&#127968;</span> Mobile & Remote Service</div>
        </div>
        <h3 style="color:#00ffce; margin:24px 0 12px 0;">Why Choose Sync Tech?</h3>
        <div style="text-align:center; margin-bottom:14px;">
            <a onclick="alert('To get support:\\n\\n1. Press Windows + R\\n2. Type quickassist\\n3. Press Enter\\n4. Follow the on-screen instructions.')" class="btn-black" tabindex="0" style="display:inline-block; cursor:pointer; font-size:1.01rem; padding:8px 18px; margin-bottom:8px;">&#128187; Remote Assistance</a>
        </div>
        <ul>
            <li>✔ We come to you — at home or your workplace (Wellington)</li>
            <li>✔ Remote support available in Wellington</li>
            <li>✔ No obligation & no fix, no fee</li>
            <li>✔ Friendly, fast, and experienced techs</li>
            <li>✔ Same-day appointments available</li>
        </ul>
    </div>
    HTML;
    ?>
  </main>

  <footer class="footer">
    <div class="container">
      <p>&copy; 2025 Sync Tech. Expert Mobile IT Help, Wherever You Are.</p>
    </div>
  </footer>

  <script src="js/script.js" defer></script>
  <script>
    function validateContactForm() {
      // Remove reCAPTCHA validation for mailto: forms
      return true;
    }
  </script>
</body>
</html>