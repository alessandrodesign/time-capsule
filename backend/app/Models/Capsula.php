<?php

namespace App\Models;

use App\Core\Database;

class Capsula
{
    public int $id;
    public string $nome_remetente;
    public string $email_remetente;
    public string $mensagem;
    public string $data_abertura;
    public int $notificada;
    public string $criado_em;

    public array $destinatarios = [];

    public static function create(array $data, array $destinatarios): int
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("INSERT INTO capsulas (nome_remetente, email_remetente, mensagem, data_abertura) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $data['nome_remetente'],
            $data['email_remetente'],
            $data['mensagem'],
            $data['data_abertura']
        ]);
        $capsulaId = (int)$pdo->lastInsertId();

        $stmtDest = $pdo->prepare("INSERT INTO destinatarios (capsula_id, nome, contato, tipo_contato) VALUES (?, ?, ?, ?)");
        foreach ($destinatarios as $dest) {
            $stmtDest->execute([
                $capsulaId,
                $dest['nome'],
                $dest['contato'],
                $dest['tipo_contato']
            ]);
        }

        return $capsulaId;
    }

    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM capsulas WHERE id = ?");
        $stmt->execute([$id]);
        $capsula = $stmt->fetch();
        if (!$capsula) return null;

        $stmtDest = $pdo->prepare("SELECT nome, contato, tipo_contato FROM destinatarios WHERE capsula_id = ?");
        $stmtDest->execute([$id]);
        $capsula['destinatarios'] = $stmtDest->fetchAll();

        return $capsula;
    }

    public static function listByUserEmail(string $email): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM capsulas WHERE email_remetente = ? ORDER BY criado_em DESC");
        $stmt->execute([$email]);
        $capsulas = $stmt->fetchAll();

        foreach ($capsulas as &$capsula) {
            $stmtDest = $pdo->prepare("SELECT nome, contato, tipo_contato FROM destinatarios WHERE capsula_id = ?");
            $stmtDest->execute([$capsula['id']]);
            $capsula['destinatarios'] = $stmtDest->fetchAll();
        }

        return $capsulas;
    }

    public static function deleteById(int $id): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("DELETE FROM destinatarios WHERE capsula_id = ?");
        $stmt->execute([$id]);

        $stmt = $pdo->prepare("DELETE FROM capsulas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function getCapsulasParaNotificar(): array
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("SELECT * FROM capsulas WHERE data_abertura <= NOW() AND notificada = 0");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function marcarNotificada(int $id): bool
    {
        $pdo = Database::getConnection();

        $stmt = $pdo->prepare("UPDATE capsulas SET notificada = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}