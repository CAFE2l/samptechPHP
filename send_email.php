<!-- #?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';

if (!isset($_POST['submit_contact'])) {
    header("Location: index.php");
    exit;
}

$name    = htmlspecialchars($_POST['name']);
$phone   = htmlspecialchars($_POST['phone']);
$email   = htmlspecialchars($_POST['email']);
$device  = htmlspecialchars($_POST['device']);
$problem = htmlspecialchars($_POST['problem']);

$mail = new PHPMailer(true);

try {
    // CONFIG SMTP
    $mail->isSMTP();
    $mail->Host       = 'joaovsampaio.dev.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'gutiajs@gmail.com'; // SEU EMAIL
    $mail->Password   = 'SENHA_DE_APP_DO_GMAIL';     // NÃO é sua senha normal
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // EMAIL
    $mail->setFrom($email, $name);
    $mail->addAddress('joaovsampaio.dev@gmail.com', 'SampTech');

    $mail->isHTML(true);
    $mail->Subject = 'Novo Orçamento - SampTech';
    $mail->Body = "
        <h2>Nova Solicitação de Orçamento</h2>
        <p><strong>Nome:</strong> $name</p>
        <p><strong>Telefone:</strong> $phone</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Equipamento:</strong> $device</p>
        <p><strong>Problema:</strong><br>$problem</p>
    ";

    $mail->send();
    $_SESSION['success'] = "Solicitação enviada com sucesso! Em breve entraremos em contato.";
} catch (Exception $e) {
    $_SESSION['error'] = "Erro ao enviar mensagem. Tente novamente.";
}

header("Location: index.php#contato");
exit; -->