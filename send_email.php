<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = strip_tags(trim($_POST["full_name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = strip_tags(trim($_POST["phone"]));
    $language = strip_tags(trim($_POST["language"]));
    $interest = strip_tags(trim($_POST["interest"]));

    // Map interest code to full label
    $interestOptions = [
        'lmv' => 'Light Motor Vehicle (LMV)',
        'mc' => 'Motorcycle',
        'hlb' => 'Heavy & Light Bus',
        'ht' => 'Heavy Truck',
        'hf' => 'Heavy Forklift',
        'lf' => 'Light Forklift'
    ];
    $interestLabel = isset($interestOptions[$interest]) ? $interestOptions[$interest] : $interest;

    // Check required fields
    if (empty($fullName) || empty($email) || empty($phone) || empty($interest)) {
        header("Location: index.html?status=error&message=Missing required fields");
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = 2;                                    // Keep disabled to allow redirect
        $mail->isSMTP();                                            
        $mail->Host       = 'localhost';                            
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'no-reply@ramadan.bdc.ae';              
        $mail->Password   = '';                   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;                                  
        $mail->SMTPAutoTLS = true;
        $mail->Port       = 587;                                     

        // SSL Options (Essential for local cPanel bypass)
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        //Recipients
        $mail->setFrom('no-reply@ramadan.bdc.ae', 'Belhasa Driving Center');
        $mail->addAddress('mohamed@ramadan.bdc.ae');               // Receive leads here
        $mail->addAddress('ibrahimhumayun@gmail.com');             // External address as primary recipient
        $mail->addReplyTo($email, $fullName);                      // Reply to the user who filled the form

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'New Ramadan Offer Lead: ' . $fullName;
                $date = date('F j, Y');
                $time = date('g:i A');
                $mail->Body = '
                <!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                    <title>New Registration – Ramadan Driving License Offer</title>
                    <style>
                        * { box-sizing: border-box; }
                        body { margin: 0; padding: 0; background-color: #f0f0f0; font-family: Arial, sans-serif; }
                        .wrapper { width: 100%; background-color: #f0f0f0; padding: 40px 16px; }
                        .container { max-width: 540px; margin: 0 auto; background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.10); }
                        .header { background-color: #E39E3D; padding: 28px 36px 24px; border-bottom: 4px solid #c8831f; }
                        .badge { background-color: rgba(0,0,0,0.12); color: #fff; font-size: 11px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; padding: 3px 10px; border-radius: 20px; display: inline-block; margin-bottom: 10px; }
                        .header h1 { margin: 0 0 4px; color: #fff; font-size: 20px; font-weight: 800; letter-spacing: -0.3px; }
                        .header p { margin: 0; color: #fff; font-size: 12.5px; }
                        .body { padding: 30px 36px; }
                        .body > p { font-size: 13.5px; color: #000000; margin: 0 0 22px; line-height: 1.6; }
                        .fields { border: 1.5px solid #e8e8e8; border-radius: 8px; overflow: hidden; margin-bottom: 24px; }
                        .field-row { display: flex; align-items: stretch; border-bottom: 1.5px solid #e8e8e8; }
                        .field-row:last-child { border-bottom: none; }
                        .field-key { background-color: #fafafa; padding: 13px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #888; width: 38%; min-width: 38%; border-right: 1.5px solid #e8e8e8; display: flex; align-items: center; }
                        .field-val { padding: 13px 16px; font-size: 14px; color: #000000; font-weight: 500; display: flex; align-items: center; flex: 1; }
                        .note { background-color: #fffbf2; border-left: 3px solid #E39E3D; border-radius: 4px; padding: 12px 16px; font-size: 13px; color: #000; line-height: 1.6; margin: 0; }
                        .footer { background-color: #f8f8f8; padding: 16px 36px; border-top: 1px solid #eeeeee; text-align: center; }
                        .footer p { margin: 0; font-size: 11.5px; color: #bbbbbb; }
                    </style>
                </head>
                <body>
                    <div class="wrapper">
                        <div class="container">
                            <div class="header">
                                <div class="badge">NEW SUBMISSION</div>
                                <h1>Ramadan Driving License Offer</h1>
                                <p>Received on <strong>' . $date . '</strong> at <strong>' . $time . '</strong></p>
                            </div>
                            <div class="body">
                                <p>A new registration has just been submitted. Please find the applicant\'s details below.</p>
                                <div class="fields">
                                    <div class="field-row">
                                        <div class="field-key">Full Name</div>
                                        <div class="field-val">' . htmlspecialchars($fullName) . '</div>
                                    </div>
                                    <div class="field-row">
                                        <div class="field-key">Email</div>
                                        <div class="field-val">' . htmlspecialchars($email) . '</div>
                                    </div>
                                    <div class="field-row">
                                        <div class="field-key">Phone Number</div>
                                        <div class="field-val">' . htmlspecialchars($phone) . '</div>
                                    </div>
                                    <div class="field-row">
                                        <div class="field-key">Preferred Language</div>
                                        <div class="field-val">' . htmlspecialchars($language) . '</div>
                                    </div>
                                    <div class="field-row">
                                        <div class="field-key">Interested In</div>
                                        <div class="field-val">' . htmlspecialchars($interestLabel) . '</div>
                                    </div>
                                </div>
                                <p class="note">Please follow up with this lead at your earliest convenience.</p>
                            </div>
                            <div class="footer">
                                <p>This is an automated notification from your registration form.</p>
                            </div>
                        </div>
                    </div>
                </body>
                </html>';
                $mail->AltBody = "Name: {$fullName}\nEmail: {$email}\nPhone: {$phone}\nLanguage: {$language}\nInterest: {$interest}";

        $mail->send();
        
        // Handle AJAX Response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'success', 'message' => 'Thank you! Your message has been sent successfully.']);
            exit;
        }

        header("Location: index.html?status=success");
    } catch (Exception $e) {
        $errorMessage = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
            exit;
        }

        header("Location: index.html?status=error&message=" . urlencode($errorMessage));
    }
} else {
    header("Location: index.html");
    exit;
}
?>
