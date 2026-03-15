<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$autoload = dirname(dirname(__FILE__)) . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // ── 1. Validate OTP ──────────────────────────────────────────────────────
    $submitted_code = trim($_POST['otp_code'] ?? '');
    $otp = $_SESSION['otp_data'] ?? null;

    if (empty($submitted_code)) {
        throw new Exception('Verification code is required.');
    }
    if (!$otp) {
        throw new Exception('No verification session found. Please request a new code.');
    }
    if ($otp['used']) {
        throw new Exception('This verification code has already been used.');
    }
    if (time() > $otp['expires']) {
        unset($_SESSION['otp_data']);
        throw new Exception('Verification code has expired. Please request a new one.');
    }
    if (!hash_equals((string)$otp['code'], (string)$submitted_code)) {
        throw new Exception('Invalid verification code. Please try again.');
    }

    $_SESSION['otp_data']['used'] = true;

    // ── 2. Validate form fields ───────────────────────────────────────────────
    $full_name      = sanitize($_POST['full_name'] ?? '');
    $email          = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone          = sanitize($_POST['phone'] ?? '');
    $service_needed = sanitize($_POST['service_needed'] ?? '');
    $message        = sanitize($_POST['message'] ?? '');

    if (empty($full_name)) {
        throw new Exception('Full name is required.');
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Valid email is required.');
    }
    if (strtolower($email) !== strtolower($otp['email'])) {
        throw new Exception('The email address does not match the verified email.');
    }
    if (empty($message)) {
        throw new Exception('Message is required.');
    }

    // ── 3. Save to database ───────────────────────────────────────────────────
    $query = "INSERT INTO contact_messages (full_name, email, phone, service_needed, message) VALUES (?, ?, ?, ?, ?)";
    $stmt  = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception($conn->error);
    }
    $stmt->bind_param("sssss", $full_name, $email, $phone, $service_needed, $message);

    if ($stmt->execute()) {
        unset($_SESSION['otp_data']);

        // ── 4. Send admin notification email ─────────────────────────────────
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            try {
                // ── Fix timezone for accurate date/time ──
                date_default_timezone_set('Asia/Manila');
                $received_date = date('F j, Y');       // e.g. March 16, 2026
                $received_time = date('g:i A');         // e.g. 2:18 PM
                $received_day  = date('l');             // e.g. Sunday

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'keithdaniellereyes@gmail.com';
                $mail->Password   = 'rgxf fubs yjot dmgs';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;
                $mail->CharSet    = 'UTF-8';

                $site_root   = rtrim(str_replace('/backend', '', BASE_URL), '/') . '/';
                $logo_url    = $site_root . 'css/assets/logo.png';
                $admin_url   = $site_root . 'admin/dashboard.php?page=messages';

                $mail->setFrom('keithdaniellereyes@gmail.com', 'NAM Builders Website');
                $mail->addAddress('keithdaniellereyes@gmail.com', 'NAM Builders Admin');
                $mail->isHTML(true);
                $mail->Subject = 'New Inquiry: ' . $full_name . ' — NAM Builders';

                // ── Optional rows ──
                $phone_row = $phone ? '
                <tr>
                    <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F8FAFC;border-bottom:1px solid #E2E8F0;width:130px;vertical-align:top;">Phone</td>
                    <td style="padding:12px 20px;font-size:13px;color:#0F172A;border-bottom:1px solid #E2E8F0;vertical-align:top;">' . htmlspecialchars($phone) . '</td>
                </tr>' : '';

                $service_row = $service_needed ? '
                <tr>
                    <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F8FAFC;border-bottom:1px solid #E2E8F0;width:130px;vertical-align:top;">Service</td>
                    <td style="padding:12px 20px;font-size:13px;border-bottom:1px solid #E2E8F0;vertical-align:top;">
                        <span style="display:inline-block;background:#EFF6FF;color:#1565C0;border:1px solid #BFDBFE;border-radius:50px;padding:2px 12px;font-size:12px;font-weight:700;">' . htmlspecialchars($service_needed) . '</span>
                    </td>
                </tr>' : '';

                $mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>New Inquiry - NAM Builders</title>
</head>
<body style="margin:0;padding:0;background:#F0F4FA;font-family:\'Segoe UI\',Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F0F4FA;padding:40px 16px;">
<tr><td align="center">

  <!-- Card -->
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#FFFFFF;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(21,101,192,0.10);">

    <!-- ── HEADER ── -->
    <tr>
      <td style="background:linear-gradient(135deg,#0D47A1 0%,#1565C0 55%,#1976D2 100%);padding:0;">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td style="padding:32px 36px 24px;">
              <!-- Logo -->
              <table cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                <tr>
                  <td style="background:rgba(255,255,255,0.15);border-radius:10px;padding:8px 14px;display:inline-block;">
                    <img src="' . $logo_url . '" alt="NAM Builders" height="36"
                         style="height:36px;width:auto;display:block;vertical-align:middle;"
                         onerror="this.style.display=\'none\'">
                  </td>
                </tr>
              </table>
              <!-- Title -->
              <h1 style="color:#FFFFFF;margin:0 0 6px;font-size:24px;font-weight:800;letter-spacing:-0.3px;line-height:1.2;">
                New Website Inquiry
              </h1>
              <p style="color:rgba(255,255,255,0.75);margin:0;font-size:13px;">
                ' . $received_day . ', ' . $received_date . ' &nbsp;&bull;&nbsp; ' . $received_time . ' (Philippine Time)
              </p>
            </td>
            <!-- Decorative circle -->
            <td style="padding:0 36px 0 0;vertical-align:bottom;text-align:right;width:80px;">
              <div style="width:70px;height:70px;border-radius:50%;background:rgba(255,255,255,0.08);display:inline-block;"></div>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- ── ALERT BANNER ── -->
    <tr>
      <td style="background:#FFFBEB;border-left:4px solid #F59E0B;padding:14px 36px;">
        <p style="margin:0;color:#92400E;font-size:13px;font-weight:600;line-height:1.5;">
          You have a new message from your website. Review it in the admin panel and reply directly to the sender.
        </p>
      </td>
    </tr>

    <!-- ── SENDER DETAILS ── -->
    <tr>
      <td style="padding:28px 36px 8px;">
        <p style="margin:0 0 14px;font-size:11px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:#1565C0;border-bottom:2px solid #E2E8F0;padding-bottom:10px;">
          Sender Information
        </p>
        <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E2E8F0;border-radius:10px;overflow:hidden;">
          <tr>
            <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F8FAFC;border-bottom:1px solid #E2E8F0;width:130px;vertical-align:top;">Full Name</td>
            <td style="padding:12px 20px;font-size:13px;font-weight:700;color:#0F172A;border-bottom:1px solid #E2E8F0;vertical-align:top;">' . htmlspecialchars($full_name) . '</td>
          </tr>
          <tr>
            <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F8FAFC;border-bottom:1px solid #E2E8F0;vertical-align:top;">Email</td>
            <td style="padding:12px 20px;border-bottom:1px solid #E2E8F0;vertical-align:top;">
              <a href="mailto:' . htmlspecialchars($email) . '" style="color:#1565C0;font-size:13px;font-weight:600;text-decoration:none;">' . htmlspecialchars($email) . '</a>
            </td>
          </tr>
          ' . $phone_row . '
          ' . $service_row . '
          <tr>
            <td style="padding:12px 20px;font-size:13px;font-weight:600;color:#64748B;background:#F8FAFC;vertical-align:top;">Received</td>
            <td style="padding:12px 20px;font-size:13px;color:#0F172A;vertical-align:top;">' . $received_day . ', ' . $received_date . ' at ' . $received_time . '</td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- ── CTA BUTTON ── -->
    <tr>
      <td style="padding:24px 36px 32px;text-align:center;">
        <p style="margin:0 0 16px;font-size:13px;color:#64748B;">
          The full message is available in your admin panel.
        </p>
        <a href="' . $admin_url . '"
           style="display:inline-block;background:linear-gradient(135deg,#1565C0,#1976D2);color:#FFFFFF;text-decoration:none;padding:14px 36px;border-radius:8px;font-size:15px;font-weight:700;letter-spacing:0.02em;box-shadow:0 4px 14px rgba(21,101,192,0.35);">
          View Message in Admin Panel
        </a>
      </td>
    </tr>

    <!-- ── DIVIDER ── -->
    <tr>
      <td style="padding:0 36px;"><hr style="border:none;border-top:1px solid #E2E8F0;margin:0;"></td>
    </tr>

    <!-- ── FOOTER ── -->
    <tr>
      <td style="padding:18px 36px 24px;text-align:center;">
        <p style="margin:0 0 4px;font-size:12px;color:#94A3B8;font-weight:600;">
          NAM Builders and Supply Corp.
        </p>
        <p style="margin:0;font-size:11px;color:#CBD5E1;">
          RNA Building Brgy. Santiago Malvar, Batangas &nbsp;&bull;&nbsp; nam.nswt@myahoo.com
        </p>
        <p style="margin:8px 0 0;font-size:11px;color:#CBD5E1;">
          &copy; ' . date('Y') . ' NAM Builders and Supply Corp. This is an automated notification.
        </p>
      </td>
    </tr>

  </table>
  <!-- /Card -->

</td></tr>
</table>

</body>
</html>';

                $mail->AltBody = "New inquiry from: {$full_name}\nEmail: {$email}\nPhone: {$phone}\nService: {$service_needed}\nReceived: {$received_day}, {$received_date} at {$received_time} (Philippine Time)\n\nView in admin: {$admin_url}";

                $mail->send();
            } catch (\Exception $mailEx) {
                error_log('Admin notification email failed: ' . $mailEx->getMessage());
            }
        }

        $response['success'] = true;
        $response['message'] = 'Thank you, ' . htmlspecialchars($full_name) . '! Your message has been received. We will contact you soon.';
    } else {
        throw new Exception($stmt->error);
    }
    $stmt->close();

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);