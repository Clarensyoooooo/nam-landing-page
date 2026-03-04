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
    // ── 1. Validate OTP first ─────────────────────────────────────────────────
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

    // Mark OTP as used immediately to prevent replay attacks
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

        // ── 4. Send email notification to admin ──────────────────────────────
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'keithdaniellereyes@gmail.com';
                $mail->Password   = 'rgxf fubs yjot dmgs';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                $logo_url = BASE_URL . 'css/assets/logo.png';
                $mail->setFrom('keithdaniellereyes@gmail.com', 'NAM Builders Website');
                $mail->addAddress('keithdaniellereyes@gmail.com', 'NAM Builders Admin');
                $mail->isHTML(true);
                $mail->Subject = '📬 New Inquiry from ' . $full_name . ' — NAM Builders Website';

                $service_row = $service_needed
                    ? '<tr><td style="padding:10px 16px;font-weight:600;color:#4A5568;background:#f9fafb;border-bottom:1px solid #e2e8f0;width:140px;">Service</td><td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;">' . htmlspecialchars($service_needed) . '</td></tr>'
                    : '';
                $phone_row = $phone
                    ? '<tr><td style="padding:10px 16px;font-weight:600;color:#4A5568;background:#f9fafb;border-bottom:1px solid #e2e8f0;">Phone</td><td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;">' . htmlspecialchars($phone) . '</td></tr>'
                    : '';

                $mail->Body = '
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f0f4fa;font-family:Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4fa;padding:40px 0;">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.10);">
        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#0D47A1 0%,#1565C0 60%,#1E88E5 100%);padding:32px 36px;">
            <table width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td>
                  <img src="' . $logo_url . '" alt="NAM Builders" style="height:48px;width:auto;object-fit:contain;margin-bottom:14px;display:block;" onerror="this.style.display=\'none\'">
                  <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;letter-spacing:0.02em;">New Website Inquiry</h1>
                  <p style="color:rgba(255,255,255,0.8);margin:6px 0 0;font-size:14px;">Received on ' . date('F j, Y \a\t g:i A') . '</p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <!-- Alert banner -->
        <tr>
          <td style="background:#FFF8E1;border-bottom:2px solid #FFE082;padding:12px 36px;">
            <p style="margin:0;color:#856404;font-size:13px;font-weight:600;">⚡ A new message has been submitted through the NAM Builders website contact form. Please review and respond promptly.</p>
          </td>
        </tr>
        <!-- Sender Details -->
        <tr>
          <td style="padding:28px 36px 10px;">
            <h2 style="font-size:13px;font-weight:800;color:#1565C0;letter-spacing:0.1em;text-transform:uppercase;margin:0 0 16px;padding-bottom:8px;border-bottom:2px solid #e2e8f0;">Sender Information</h2>
            <table width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
              <tr>
                <td style="padding:10px 16px;font-weight:600;color:#4A5568;background:#f9fafb;border-bottom:1px solid #e2e8f0;width:140px;">Full Name</td>
                <td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;font-weight:700;color:#0A0A0A;">' . htmlspecialchars($full_name) . '</td>
              </tr>
              <tr>
                <td style="padding:10px 16px;font-weight:600;color:#4A5568;background:#f9fafb;border-bottom:1px solid #e2e8f0;">Email</td>
                <td style="padding:10px 16px;border-bottom:1px solid #e2e8f0;"><a href="mailto:' . htmlspecialchars($email) . '" style="color:#1565C0;font-weight:600;">' . htmlspecialchars($email) . '</a></td>
              </tr>
              ' . $phone_row . '
              ' . $service_row . '
            </table>
          </td>
        </tr>
        <!-- Message -->
        <tr>
          <td style="padding:16px 36px 28px;">
            <h2 style="font-size:13px;font-weight:800;color:#1565C0;letter-spacing:0.1em;text-transform:uppercase;margin:0 0 12px;padding-bottom:8px;border-bottom:2px solid #e2e8f0;">Message</h2>
            <div style="background:#f9fafb;border:1px solid #e2e8f0;border-left:4px solid #1565C0;border-radius:8px;padding:16px 20px;">
              <p style="margin:0;color:#374151;line-height:1.8;font-size:15px;">' . nl2br(htmlspecialchars($message)) . '</p>
            </div>
          </td>
        </tr>
        <!-- CTA -->
        <tr>
          <td style="padding:0 36px 32px;text-align:center;">
            <a href="' . BASE_URL . 'admin/dashboard.php?page=messages" style="display:inline-block;background:linear-gradient(135deg,#1565C0,#1E88E5);color:#fff;text-decoration:none;padding:14px 32px;border-radius:8px;font-weight:700;font-size:15px;letter-spacing:0.03em;">View in Admin Panel →</a>
            <p style="margin:16px 0 0;font-size:12px;color:#9CA3AF;">You can reply directly to this sender from the admin messages panel.</p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#f9fafb;border-top:1px solid #e2e8f0;padding:18px 36px;text-align:center;">
            <p style="margin:0;color:#9CA3AF;font-size:12px;">&copy; ' . date('Y') . ' NAM Builders and Supply Corp. — Automated notification from your website.</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';
                $mail->AltBody = "New inquiry from: $full_name\nEmail: $email\nPhone: $phone\nService: $service_needed\n\nMessage:\n$message\n\nView in admin: " . BASE_URL . "admin/dashboard.php?page=messages";
                $mail->send();
            } catch (\Exception $mailEx) {
                // Silently fail — don't block form submission if admin email fails
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