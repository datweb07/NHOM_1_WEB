<?php

require __DIR__ . '/../../../vendor/autoload.php';

class RedisService
{
    private static ?RedisService $instance = null;
    private $redis = null;
    private bool $connected = false;

    private function __construct()
    {
        $this->connect();
    }

    public static function getInstance(): RedisService
    {
        if (self::$instance === null) {
            self::$instance = new RedisService();
        }
        return self::$instance;
    }

    private function connect(): void
    {
        if (!class_exists('\Predis\Client')) {
            error_log('[RedisService] Predis library not found. Run: composer require predis/predis');
            $this->connected = false;
            return;
        }

        try {
            $host = $_ENV['REDIS_HOST'] ?? '127.0.0.1';
            $port = (int)($_ENV['REDIS_PORT'] ?? 6379);
            $password = $_ENV['REDIS_PASSWORD'] ?? null;
            $database = (int)($_ENV['REDIS_DB'] ?? 0);

            $parameters = [
                'scheme' => 'tcp',
                'host'   => $host,
                'port'   => $port,
            ];

            if ($password !== null && $password !== 'null' && $password !== '') {
                $parameters['password'] = $password;
            }

            if ($database > 0) {
                $parameters['database'] = $database;
            }

            $options = [
                'parameters' => [
                    'timeout' => 2.5,
                ],
            ];

            $this->redis = new \Predis\Client($parameters, $options);
            
            $response = $this->redis->ping();
            
            $responsePayload = null;
            if (is_object($response) && method_exists($response, 'getPayload')) {
                $responsePayload = $response->getPayload();
            } elseif (is_string($response)) {
                $responsePayload = $response;
            } elseif ($response === true) {
                $responsePayload = 'PONG';
            }
            
            if ($responsePayload === 'PONG' || $response === true) {
                $this->connected = true;
                error_log('[RedisService] Connected to Redis via Predis successfully');
            } else {
                throw new Exception("Redis PING failed - unexpected response: " . var_export($response, true));
            }
        } catch (Exception $e) {
            error_log('[RedisService] Predis connection failed: ' . $e->getMessage());
            $this->connected = false;
            $this->redis = null;
        }
    }

    public function isConnected(): bool
    {
        return $this->connected && $this->redis !== null;
    }

    public function get(string $key)
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            return $this->redis->get($key);
        } catch (Exception $e) {
            error_log('[RedisService] Get failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }

    public function set(string $key, string $value, ?int $ttl = null): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            if ($ttl !== null) {
                $result = $this->redis->setex($key, $ttl, $value);
            } else {
                $result = $this->redis->set($key, $value);
            }
            
            return $this->convertStatusToBool($result);
        } catch (Exception $e) {
            error_log('[RedisService] Set failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }

    public function delete(string $key): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            return $this->redis->del($key) > 0;
        } catch (Exception $e) {
            error_log('[RedisService] Delete failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }

    public function exists(string $key): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            return $this->redis->exists($key) > 0;
        } catch (Exception $e) {
            error_log('[RedisService] Exists check failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }

    public function expire(string $key, int $ttl): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            $result = $this->redis->expire($key, $ttl);
            return (int)$result === 1;
        } catch (Exception $e) {
            error_log('[RedisService] Expire failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }

    public function ttl(string $key): int
    {
        if (!$this->isConnected()) {
            return -2;
        }

        try {
            return $this->redis->ttl($key);
        } catch (Exception $e) {
            error_log('[RedisService] TTL check failed for key ' . $key . ': ' . $e->getMessage());
            return -2;
        }
    }

    public function increment(string $key, int $value = 1)
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            if ($value === 1) {
                return $this->redis->incr($key);
            } else {
                return $this->redis->incrBy($key, $value);
            }
        } catch (Exception $e) {
            error_log('[RedisService] Increment failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }

    public function decrement(string $key, int $value = 1)
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            if ($value === 1) {
                return $this->redis->decr($key);
            } else {
                return $this->redis->decrBy($key, $value);
            }
        } catch (Exception $e) {
            error_log('[RedisService] Decrement failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }

    public function sAdd(string $key, string ...$members): int
    {
        if (!$this->isConnected()) {
            return 0;
        }

        try {
            return $this->redis->sadd($key, ...$members);
        } catch (Exception $e) {
            error_log('[RedisService] sAdd failed for key ' . $key . ': ' . $e->getMessage());
            return 0;
        }
    }

    public function sRem(string $key, string ...$members): int
    {
        if (!$this->isConnected()) {
            return 0;
        }

        try {
            return $this->redis->srem($key, ...$members);
        } catch (Exception $e) {
            error_log('[RedisService] sRem failed for key ' . $key . ': ' . $e->getMessage());
            return 0;
        }
    }

    public function sIsMember(string $key, string $member): bool
    {
        if (!$this->isConnected()) {
            return false;
        }

        try {
            return (bool)$this->redis->sismember($key, $member);
        } catch (Exception $e) {
            error_log('[RedisService] sIsMember failed for key ' . $key . ': ' . $e->getMessage());
            return false;
        }
    }

    public function sMembers(string $key): array
    {
        if (!$this->isConnected()) {
            return [];
        }

        try {
            return $this->redis->smembers($key);
        } catch (Exception $e) {
            error_log('[RedisService] sMembers failed for key ' . $key . ': ' . $e->getMessage());
            return [];
        }
    }

    public function sCard(string $key): int
    {
        if (!$this->isConnected()) {
            return 0;
        }

        try {
            return $this->redis->scard($key);
        } catch (Exception $e) {
            error_log('[RedisService] sCard failed for key ' . $key . ': ' . $e->getMessage());
            return 0;
        }
    }

    public function close(): void
    {
        if ($this->redis !== null) {
            try {
                $this->redis->disconnect();
            } catch (Exception $e) {
                error_log('[RedisService] Close failed: ' . $e->getMessage());
            }
            $this->redis = null;
            $this->connected = false;
        }
    }

    private function convertStatusToBool($result): bool
    {
        if (is_object($result) && method_exists($result, 'getPayload')) {
            $payload = $result->getPayload();
            return $payload === 'OK' || $payload === true;
        }
        
        if (is_string($result)) {
            return $result === 'OK';
        }
        
        return (bool)$result;
    }

    private function __clone() {}

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
