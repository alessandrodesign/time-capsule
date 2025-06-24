<?php

namespace App\Services;

use App\Core\Database;
use App\Models\Capsula;
use RuntimeException;

class Notifier
{
    private array $capsulas;
    private Notification $notification;

    public function __construct()
    {
        $this->capsulas = Capsula::getCapsulasParaNotificar();
        $this->notification = new Notification();
        $this->notify();
    }

    private function notify(): void
    {
        foreach ($this->capsulas as $capsula) {
            $capsulaId = $capsula['id'];
            $mensagem = "Olá! Sua cápsula do tempo enviada por {$capsula['nome_remetente']} está disponível para leitura:\n\n";
            $mensagem .= $capsula['mensagem'] . "\n\n";
            $mensagem .= "Data de abertura: {$capsula['data_abertura']}";

            // Buscar destinatários
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare("SELECT nome, contato, tipo_contato FROM destinatarios WHERE capsula_id = ?");
            $stmt->execute([$capsulaId]);
            $destinatarios = $stmt->fetchAll();

            foreach ($destinatarios as $dest) {
                $enviado = false;
                switch ($dest['tipo_contato']) {
                    case 'email':
                        $enviado = $this->notification->enviarEmail($dest['contato'], $mensagem, $dest['nome']);
                        break;
                    case 'whatsapp':
                        $enviado = $this->notification->enviarWhatsApp($dest['contato'], $mensagem);
                        break;
                    case 'telegram':
                        $enviado = $this->notification->enviarTelegram($dest['contato'], $mensagem);
                        break;
                    case 'sms':
                        $enviado = $this->notification->enviarSMS($dest['contato'], $mensagem);
                        break;
                }
                if ($enviado) {
                    echo "Notificação enviada para {$dest['nome']} via {$dest['tipo_contato']}\n";
                } else {
                    $message = "Falha ao enviar notificação para {$dest['nome']} via {$dest['tipo_contato']}";
                    error_log($message);
                    throw new RuntimeException($message);
                }
            }

            // Marcar como notificada
            Capsula::marcarNotificada($capsulaId);
        }
    }
}