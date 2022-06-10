<?php

namespace Proxy;

use Predis\Client;

class Redis {

    /** @var Client $client Redis Client Connector */
	protected static Client $client;

    /** @var string $redis_scheme Scheme for Redis Connection */
    private static string $redis_scheme;

    /** @var int $redis_port Port for Redis Connection */
    private static int $redis_port;

    /** @var string $redis_host Host for Redis Connection */
    private static string $redis_host;

    /**
     * CConfigure the connection to redis server
     * @param string|null $redis_scheme
     * @param string|null $redis_host
     * @param int|null $redis_port
     * @return void
     */
    public static function setRedisConnection(
        string $redis_scheme = null,
        string $redis_host = null,
        int $redis_port = null
    ) : void {
        self::$redis_scheme = $redis_scheme ?? 'tcp';
        self::$redis_host = $redis_host ?? '127.0.0.1';
        self::$redis_port = $redis_port ?? 6379;
    }
	
	public static function __callStatic(
        string $method_name,
        $arguments
    ) {

        if(!self::$redis_host) {
            self::setRedisConnection();
        }

		$params = array(
			'scheme' => self::$redis_scheme,
			'host'   => self::$redis_host,
			'port'   => self::$redis_port,
		);
		
		if(!static::$client){
			static::$client = new Client($params);
		}
		
		return call_user_func_array(array(static::$client, $method_name), $arguments);
	}
}
