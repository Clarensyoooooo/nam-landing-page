<?php
// Output JSON header immediately so ANY error/warning still returns parseable JSON
header('Content-Type: application/json');
error_reporting(0);

require_once '../config/database.php';
require_once '../includes/functions.php';

// Session check
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized. Please log in again.']);
    exit();
}

// Load PHPMailer
$autoload = dirname(dirname(__FILE__)) . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    echo json_encode(['success' => false, 'message' => 'PHPMailer not found. Please run: composer require phpmailer/phpmailer']);
    exit();
}
require_once $autoload;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;

$response = ['success' => false, 'message' => ''];

try {
    $message_id = intval($_POST['message_id'] ?? 0);
    $reply_body = trim($_POST['reply_body'] ?? '');
    $admin_name = sanitize($_SESSION['admin_username'] ?? 'NAM Builders Admin');

    if ($message_id <= 0) {
        throw new \Exception('Invalid message ID.');
    }
    if (empty($reply_body)) {
        throw new \Exception('Reply message cannot be empty.');
    }

    // Fetch original message
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
    if (!$stmt) {
        throw new \Exception('Database error: ' . $conn->error);
    }
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $original = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$original) {
        throw new \Exception('Original message not found.');
    }

    // Fix timezone
    date_default_timezone_set('Asia/Manila');
    $reply_date = date('l, F j, Y');
    $reply_time = date('g:i A');

    $to_email = $original['email'];
    $to_name  = $original['full_name'];
    $initials = strtoupper(substr(trim($to_name), 0, 1));

    // Build site root correctly (strip /backend if present)
    $site_root = rtrim(str_replace('/backend', '', BASE_URL), '/') . '/';
    $logo_url  = $site_root . 'css/assets/logo.png';

    // Convert reply line breaks to <br> for HTML
    $reply_html = nl2br(htmlspecialchars($reply_body));

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'keithdaniellereyes@gmail.com';
    $mail->Password   = 'rgxf fubs yjot dmgs';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom('keithdaniellereyes@gmail.com', 'NAM Builders and Supply Corp.');
    $mail->addAddress($to_email, $to_name);
    $mail->isHTML(true);
    $mail->Subject = 'Re: Your Inquiry — NAM Builders and Supply Corp.';

    $mail->Body = '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Reply from NAM Builders</title>
</head>
<body style="margin:0;padding:0;background:#F0F4FA;font-family:\'Segoe UI\',Arial,sans-serif;-webkit-font-smoothing:antialiased;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#F0F4FA;padding:40px 16px;">
<tr><td align="center">

  <!-- Card -->
  <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#FFFFFF;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(21,101,192,0.10);">

    <!-- ── HEADER ── -->
    <tr>
      <td style="background:linear-gradient(135deg,#0D47A1 0%,#1565C0 55%,#1976D2 100%);padding:32px 36px 28px;">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td>
              <!-- Logo pill -->
              <table cellpadding="0" cellspacing="0" style="margin-bottom:22px;">
                <tr>
                  <td style="background:rgba(255,255,255,0.15);border-radius:10px;padding:8px 14px;">
                    <img src="' . $logo_url . '" alt="NAM Builders" height="36"
                         style="height:36px;width:auto;display:block;vertical-align:middle;">
                  </td>
                </tr>
              </table>
              <!-- Greeting -->
              <h1 style="color:#FFFFFF;margin:0 0 6px;font-size:22px;font-weight:800;letter-spacing:-0.3px;line-height:1.2;">
                Response to Your Inquiry
              </h1>
              <p style="color:rgba(255,255,255,0.75);margin:0;font-size:13px;">
                ' . $reply_date . ' &nbsp;&bull;&nbsp; ' . $reply_time . ' (Philippine Time)
              </p>
            </td>
            <!-- Decorative element -->
            <td style="vertical-align:top;text-align:right;padding-left:20px;width:60px;">
              <div style="width:56px;height:56px;border-radius:50%;background:rgba(255,255,255,0.1);display:inline-block;"></div>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- ── LETTER BODY ── -->
    <tr>
      <td style="padding:36px 36px 0;">
        <!-- Salutation -->
        <p style="margin:0 0 18px;font-size:15px;color:#1E293B;line-height:1.8;">
          Hello ' . htmlspecialchars($to_name) . ',
        </p>
        <!-- Body -->
        <p style="margin:0;font-size:14px;color:#374151;line-height:1.95;white-space:pre-line;">
          ' . $reply_html . '
        </p>
      </td>
    </tr>

    <!-- ── SIGN OFF ── -->
    <tr>
      <td style="padding:28px 36px 32px;">
        <p style="margin:0 0 16px;font-size:14px;color:#374151;">Warm regards,</p>
        <p style="margin:0 0 1px;font-size:13px;font-weight:600;color:#1565C0;">NAM Builders and Supply Corp.</p>
      </td>
    </tr>

    <!-- ── DIVIDER ── -->
    <tr>
      <td style="padding:0 36px;">
        <hr style="border:none;border-top:1px solid #E2E8F0;margin:0;">
      </td>
    </tr>

    <!-- ── CONTACT INFO ── -->
    <tr>
      <td style="padding:20px 36px;">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <!-- Phone -->
            <td style="vertical-align:top;padding-right:24px;">
              <p style="margin:0 0 3px;font-size:10px;font-weight:800;letter-spacing:0.1em;text-transform:uppercase;color:#94A3B8;">Phone</p>
              <p style="margin:0;font-size:12px;color:#475569;line-height:1.6;">
                09230209877<br>09385314311<br>09568365775
              </p>
            </td>
            <!-- Email -->
            <td style="vertical-align:top;padding-right:24px;">
              <p style="margin:0 0 3px;font-size:10px;font-weight:800;letter-spacing:0.1em;text-transform:uppercase;color:#94A3B8;">Email</p>
              <p style="margin:0;font-size:12px;color:#475569;">
                <a href="mailto:nam.nswt@myahoo.com" style="color:#1565C0;text-decoration:none;">nam.nswt@myahoo.com</a>
              </p>
            </td>
            <!-- Address -->
            <td style="vertical-align:top;">
              <p style="margin:0 0 3px;font-size:10px;font-weight:800;letter-spacing:0.1em;text-transform:uppercase;color:#94A3B8;">Address</p>
              <p style="margin:0;font-size:12px;color:#475569;line-height:1.6;">
                RNA Building Brgy.<br>Santiago Malvar, Batangas
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>

    <!-- ── FOOTER ── -->
    <tr>
      <td style="background:#F8FAFC;border-top:1px solid #E2E8F0;padding:16px 36px;text-align:center;">
        <p style="margin:0 0 3px;font-size:12px;font-weight:700;color:#94A3B8;">
          NAM Builders and Supply Corp.
        </p>
        <p style="margin:0;font-size:11px;color:#CBD5E1;">
          &copy; ' . date('Y') . ' NAM Builders and Supply Corp. All rights reserved.
        </p>
      </td>
    </tr>

  </table>
  <!-- /Card -->

</td></tr>
</table>

</body>
</html>';

    $mail->AltBody = "Dear {$to_name},\n\n{$reply_body}\n\nWarm regards,\n{$admin_name}\nNAM Builders and Supply Corp.\n\nPhone: 09230209877 / 09385314311\nEmail: nam.nswt@myahoo.com\nAddress: RNA Building Brgy. Santiago Malvar, Batangas";

    $mail->send();

    // Mark message as read
    $conn->query("UPDATE contact_messages SET is_read = 1, is_replied = 1 WHERE id = " . intval($message_id));

    $response['success'] = true;
    $response['message'] = 'Reply sent successfully to ' . htmlspecialchars($to_email);

} catch (MailException $e) {
    $response['message'] = 'Mail error: ' . $e->getMessage();
} catch (\Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>