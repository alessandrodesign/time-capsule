<?php declare(strict_types=1);

namespace App\Services;

/**
 * Class SessionManager
 *
 * Gerencia sessões PHP e mensagens flash de forma abstrata.
 * Requer PHP 8.3 ou superior.
 */
class SessionManager
{
    private const string FLASH_KEY = '__flash_messages__';

    /**
     * Inicia a sessão se ainda não estiver iniciada.
     *
     * @return void
     */
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Define um valor na sessão.
     *
     * @param string $key A chave para armazenar o valor.
     * @param mixed $value O valor a ser armazenado.
     * @return void
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Obtém um valor da sessão.
     *
     * @param string $key A chave do valor a ser recuperado.
     * @param mixed|null $default O valor padrão a ser retornado se a chave não existir.
     * @return mixed|null
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Verifica se uma chave existe na sessão.
     *
     * @param string $key A chave a ser verificada.
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove uma chave da sessão.
     *
     * @param string $key A chave a ser removida.
     * @return void
     */
    public function unset(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Destrói todos os dados da sessão.
     *
     * @return void
     */
    public function destroy(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    /**
     * Adiciona uma mensagem flash.
     * As mensagens flash são armazenadas para serem exibidas na próxima requisição e depois removidas.
     *
     * @param string $message A mensagem a ser adicionada.
     * @param string $type O tipo da mensagem (e.g., 'success', 'error', 'warning', 'info').
     * @return void
     */
    public function addFlashMessage(string $message, string $type = 'info'): void
    {
        $this->start(); // Garante que a sessão esteja iniciada para armazenar mensagens flash
        $flashMessages = $this->get(self::FLASH_KEY, []);
        $flashMessages[] = ['message' => $message, 'type' => $type];
        $this->set(self::FLASH_KEY, $flashMessages);
    }

    /**
     * Obtém todas as mensagens flash e as remove da sessão.
     *
     * @return array<array{message: string, type: string}>
     */
    public function getFlashMessages(): array
    {
        $this->start(); // Garante que a sessão esteja iniciada para recuperar mensagens flash
        $flashMessages = $this->get(self::FLASH_KEY, []);
        $this->unset(self::FLASH_KEY); // Limpa as mensagens flash após a recuperação
        return $flashMessages;
    }

    /**
     * Regenera o ID da sessão para prevenir ataques de fixação de sessão.
     *
     * @param bool $deleteOldSession Se true, a sessão antiga será excluída imediatamente.
     * @return void
     */
    public function regenerateId(bool $deleteOldSession = true): void
    {
        session_regenerate_id($deleteOldSession);
    }
}