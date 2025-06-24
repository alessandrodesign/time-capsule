<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Notification
{
    public function enviarEmail(string $destinatarioEmail, string $mensagem, string $nomeDestinatario = ''): bool
    {
        $mail = new PHPMailer(true);
        try {
            // Config SMTP básico
            $mail->isSMTP();
            $mail->Host = 'smtp.seuprovedor.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'seu_email@dominio.com';
            $mail->Password = 'sua_senha';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('no-reply@capsuladotempo.com', 'Cápsula do Tempo');
            $mail->addAddress($destinatarioEmail, $nomeDestinatario);

            $mail->isHTML(true);
            $mail->Subject = 'Sua Cápsula do Tempo está disponível!';
            $mail->Body = nl2br(htmlspecialchars($mensagem));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar email para $destinatarioEmail: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function enviarWhatsApp(string $numero, string $mensagem): bool
    {
        // Exemplo com API Z-API (substitua com sua API real)
        $token = 'seu_token_zapi';
        $url = "https://api.z-api.io/instances/seu_instance_id/token/$token/send-messages";

        $data = [
            'phone' => $numero,
            'message' => $mensagem,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    public function enviarTelegram(string $handle, string $mensagem): bool
    {
        $botToken = 'seu_bot_token';
        $chatId = $handle; // Pode ser chat_id ou username com @

        $url = "https://api.telegram.org/bot$botToken/sendMessage";

        $data = [
            'chat_id' => $chatId,
            'text' => $mensagem,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    public function enviarSMS(string $numero, string $mensagem): bool
    {
        // Exemplo com API TotalVoice (substitua com sua API real)
        $token = 'seu_token_totalvoice';
        $url = "https://api.totalvoice.com.br/sms";

        $data = [
            'numero_destino' => $numero,
            'mensagem' => $mensagem,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "Access-Token: $token"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }
}