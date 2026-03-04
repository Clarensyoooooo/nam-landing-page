<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$autoload = dirname(dirname(__FILE__)) . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    jsonResponse(false, 'PHPMailer not found. Please run: composer require phpmailer/phpmailer');
}
require_once $autoload;

requireLogin();
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    $message_id   = intval($_POST['message_id'] ?? 0);
    $reply_body   = trim($_POST['reply_body'] ?? '');
    $admin_name   = sanitize($_SESSION['admin_username'] ?? 'NAM Builders Admin');

    if ($message_id <= 0) {
        throw new Exception('Invalid message ID.');
    }
    if (empty($reply_body)) {
        throw new Exception('Reply message cannot be empty.');
    }

    // Fetch original message
    $stmt = $conn->prepare("SELECT * FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $original = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$original) {
        throw new Exception('Original message not found.');
    }

    $to_email = $original['email'];
    $to_name  = $original['full_name'];
    $logo_url = BASE_URL . 'css/assets/logo.png';

    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'keithdaniellereyes@gmail.com';
    $mail->Password   = 'rgxf fubs yjot dmgs';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('keithdaniellereyes@gmail.com', 'NAM Builders & Supply Corp.');
    $mail->addAddress($to_email, $to_name);
    $mail->isHTML(true);
    $mail->Subject = 'Re: Your Inquiry — NAM Builders & Supply Corp.';

    // Format quoted original message
    $original_service = $original['service_needed'] ? '<p style="margin:4px 0;font-size:13px;color:#6B7280;"><strong>Service:</strong> ' . htmlspecialchars($original['service_needed']) . '</p>' : '';
    $original_phone   = $original['phone']          ? '<p style="margin:4px 0;font-size:13px;color:#6B7280;"><strong>Phone:</strong> '   . htmlspecialchars($original['phone'])          . '</p>' : '';

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
            <img src="' . $logo_url . '" alt="NAM Builders" style="height:48px;width:auto;object-fit:contain;margin-bottom:12px;display:block;" onerror="this.style.display=\'none\'">
            <h1 style="color:#fff;margin:0;font-size:22px;font-weight:800;">NAM Builders &amp; Supply Corp.</h1>
            <p style="color:rgba(255,255,255,0.8);margin:6px 0 0;font-size:14px;">Response to Your Inquiry</p>
          </td>
        </tr>
        <!-- Greeting -->
        <tr>
          <td style="padding:28px 36px 0;">
            <p style="margin:0 0 6px;font-size:16px;color:#0A0A0A;">Dear <strong>' . htmlspecialchars($to_name) . '</strong>,</p>
            <p style="margin:0 0 20px;font-size:14px;color:#4A5568;">Thank you for reaching out to us. Here is our response to your inquiry:</p>
          </td>
        </tr>
        <!-- Reply Body -->
        <tr>
          <td style="padding:0 36px;">
            <div style="background:#F0F4FA;border-left:4px solid #1565C0;border-radius:0 10px 10px 0;padding:20px 24px;">
              <p style="margin:0;color:#1A202C;font-size:15px;line-height:1.85;white-space:pre-line;">' . htmlspecialchars($reply_body) . '</p>
            </div>
          </td>
        </tr>
        <!-- Signature -->
        <tr>
          <td style="padding:24px 36px 20px;">
            <p style="margin:0 0 4px;font-size:14px;color:#4A5568;">Warm regards,</p>
            <p style="margin:0;font-size:15px;font-weight:700;color:#0A0A0A;">' . htmlspecialchars($admin_name) . '</p>
            <p style="margin:2px 0 0;font-size:13px;color:#1565C0;font-weight:600;">NAM Builders &amp; Supply Corp.</p>
          </td>
        </tr>
        <!-- Divider -->
        <tr><td style="padding:0 36px;"><hr style="border:none;border-top:1px solid #e2e8f0;margin:0;"></td></tr>
        <!-- Quoted original message -->
        <tr>
          <td style="padding:20px 36px 28px;">
            <p style="margin:0 0 10px;font-size:12px;font-weight:700;color:#9CA3AF;letter-spacing:0.08em;text-transform:uppercase;">Your Original Message</p>
            <div style="background:#f9fafb;border:1px solid #e2e8f0;border-radius:8px;padding:14px 18px;">
              <p style="margin:0 0 6px;font-size:13px;color:#6B7280;"><strong>From:</strong> ' . htmlspecialchars($to_name) . ' &lt;' . htmlspecialchars($to_email) . '&gt;</p>
              ' . $original_phone . '
              ' . $original_service . '
              <p style="margin:10px 0 0;font-size:13px;color:#374151;line-height:1.7;white-space:pre-line;">' . htmlspecialchars($original['message']) . '</p>
            </div>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#f9fafb;border-top:1px solid #e2e8f0;padding:18px 36px;text-align:center;">
            <p style="margin:0;color:#9CA3AF;font-size:12px;">&copy; ' . date('Y') . ' NAM Builders and Supply Corp. All rights reserved.</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>';
    $mail->AltBody = "Dear $to_name,\n\n$reply_body\n\nWarm regards,\n$admin_name\nNAM Builders & Supply Corp.";

    $mail->send();

    // Mark as read if not already
    $conn->query("UPDATE contact_messages SET is_read = 1 WHERE id = $message_id");

    $response['success'] = true;
    $response['message'] = 'Reply sent successfully to ' . htmlspecialchars($to_email);

} catch (\PHPMailer\PHPMailer\Exception $e) {
    $response['message'] = 'Mail error: ' . $e->getMessage();
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>