<?php declare(strict_types=1);

namespace App\Core;

use App\Controllers\CapsulaController;
use App\Helpers\Dependencies;
use App\Services\SessionManager;
use DI\ContainerBuilder;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Dotenv\Dotenv;

final class App
{
    private static ?App $instance = null;
    private SessionManager $session;
    private Request $request;
    private ContainerBuilder $builder;

    /**
     * @throws Exception
     */
    private function __construct()
    {
        $this->session = new SessionManager();
        $this->session->start();
        $this->request = Request::createFromGlobals();
        $this->builder = new ContainerBuilder();
        $this->setup();
    }

    public static function run(): void
    {
        self::getInstance();
    }

    public static function getInstance(): App
    {
        if (self::$instance == null) {
            self::$instance = new App();
        }
        return self::$instance;
    }

    public function __clone(): void
    {
        throw new RuntimeException('Cloning is not allowed.');
    }

    public function __wakeup(): void
    {
        throw new RuntimeException('Unserialization is not allowed.');
    }

    /**
     * @throws Exception
     */
    private function setup(): void
    {
        $this->setupEnvironment();
        $this->setupConstants();
        $this->setupDependencies();

        $this->dispatch();
    }

    private function setupEnvironment(): void
    {
        $dotenv = new Dotenv();
        $dotenv->load(__DIR__ . '/../../../.env');
    }

    private function setupConstants(): void
    {
        if (!defined("PATH_ROOT")) {
            define("PATH_ROOT", realpath(__DIR__ . '/../../'));
        }
        if (!defined("PATH_APP")) {
            define("PATH_APP", realpath(PATH_ROOT . '/app/'));
        }
        if (!defined("PATH_STORAGE")) {
            define("PATH_STORAGE", realpath(PATH_ROOT . '/storage/'));
        }
    }

    private function setupDependencies(): void
    {
        $definitions = array_merge([
            SessionManager::class => $this->session,
            Request::class => $this->request
        ], Dependencies::get());

        $this->builder->addDefinitions($definitions);
    }

    /**
     * @throws Exception
     */
    private function dispatch(): void
    {
        $method = $this->request->getMethod();
        $path = $this->request->getPathInfo();

        $container = $this->builder->build();
        $capsulaController = $container->get(CapsulaController::class);

        if ($path === '/capsulas' && $method === 'POST') {
            $response = $capsulaController->create();
        } elseif (preg_match('#^/capsulas/(\d+)$#', $path, $matches)) {
            $id = (int)$matches[1];
            if ($this->request->isMethod('GET')) {
                $response = $capsulaController->get($id);
            } elseif ($this->request->isMethod('DELETE')) {
                $response = $capsulaController->delete($id);
            } else {
                throw new Exception('Method Not Allowed', 405);
            }
        } elseif ($path === '/capsulas' && $method === 'GET') {
            $response = $capsulaController->list();
        } else {
            throw new Exception('Not Found', 404);
        }

        $response->send();
    }
}