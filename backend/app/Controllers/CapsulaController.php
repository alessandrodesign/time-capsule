<?php declare(strict_types=1);

namespace App\Controllers;

use App\Models\Capsula;
use DateTime;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CapsulaController extends Controller
{
    private function validateCapsulaData(array $data): array
    {
        $errors = [];

        if (empty($data['nome_remetente'])) {
            $errors[] = 'Nome do remetente é obrigatório.';
        }
        if (empty($data['email_remetente']) || !filter_var($data['email_remetente'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'E-mail do remetente inválido.';
        }
        if (empty($data['mensagem'])) {
            $errors[] = 'Mensagem é obrigatória.';
        }
        if (empty($data['data_abertura'])) {
            $errors[] = 'Data de abertura é obrigatória.';
        } else {
            $dt = DateTime::createFromFormat('Y-m-d H:i:s', $data['data_abertura']);
            if (!$dt) {
                $errors[] = 'Data de abertura inválida. Use formato YYYY-MM-DD HH:MM:SS.';
            } elseif ($dt <= new DateTime()) {
                $errors[] = 'Data de abertura deve ser no futuro.';
            }
        }
        if (empty($data['destinatarios']) || !is_array($data['destinatarios'])) {
            $errors[] = 'Destinatários são obrigatórios.';
        } else {
            foreach ($data['destinatarios'] as $i => $dest) {
                if (empty($dest['nome'])) {
                    $errors[] = "Nome do destinatário #$i é obrigatório.";
                }
                if (empty($dest['contato'])) {
                    $errors[] = "Contato do destinatário #$i é obrigatório.";
                }
                if (empty($dest['tipo_contato']) || !in_array($dest['tipo_contato'], ['email', 'whatsapp', 'telegram', 'sms'])) {
                    $errors[] = "Tipo de contato do destinatário #$i inválido.";
                }
                // Validação básica de contato
                if ($dest['tipo_contato'] === 'email' && !filter_var($dest['contato'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "E-mail do destinatário #$i inválido.";
                }
                // Para WhatsApp, Telegram e SMS, poderia validar formato de número/handle, mas simplificamos aqui
            }
        }

        return $errors;
    }

    public function create(): JsonResponse
    {
        $data = $this->request->toArray();

        $errors = $this->validateCapsulaData($data);

        if ($errors) {
            return $this->fail('Há erros em sua requisição', $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $capsulaData = [
            'nome_remetente' => htmlspecialchars($data['nome_remetente']),
            'email_remetente' => htmlspecialchars($data['email_remetente']),
            'mensagem' => htmlspecialchars($data['mensagem']),
            'data_abertura' => $data['data_abertura'],
        ];

        $destinatarios = array_map(function ($d) {
            return [
                'nome' => htmlspecialchars($d['nome']),
                'contato' => htmlspecialchars($d['contato']),
                'tipo_contato' => $d['tipo_contato'],
            ];
        }, $data['destinatarios']);

        $id = Capsula::create($capsulaData, $destinatarios);

        return $this->created('Criado com sucesso', ['id' => $id]);
    }

    public function get(int $id): JsonResponse
    {
        $capsula = Capsula::getById($id);
        if (!$capsula) {
            return $this->notFound('Cápsula não encontrada');
        }
        return $this->respond('Cápsula encontrada', $capsula);
    }

    public function list(): JsonResponse
    {
        $email = $this->request->query->get('email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->fail('E-mail do remetente é obrigatório para listagem');
        }

        $capsulas = Capsula::listByUserEmail($email);

        return $this->respond('Cápsulas', $capsulas);
    }

    public function delete(int $id): JsonResponse
    {
        $deleted = Capsula::deleteById($id);

        if ($deleted) {
            return $this->deleted('Cápsula deletada');
        }
        return $this->notFound('Cápsula não encontrada');
    }
}