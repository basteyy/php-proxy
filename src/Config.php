<?php
/**
 * PHP-Proxy
 *
 * Php-Proxy library to create a web-based proxy
 * @see https://github.com/Athlon1600/php-proxy
 * @license MIT
 * @author https://github.com/Athlon1600/php-proxy/graphs/contributors
 */

declare(strict_types=1);

namespace Proxy;

use Exception;
use InvalidArgumentException;
use function basteyy\VariousPhpSnippets\varDebug;

class Config
{
    /** @var array $configFiles Array which hols the files for loading */
    private static array $configFiles;

    /** @var string $path The directory where the .env file can be located. */
    protected string $path;

    /**
     * Constructor which expect the path to the .env-File
     * @param string $path
     * @throws Exception
     */
    public function __construct(?string $path = null)
    {
        if($path) {
            self::load($path);
        }
    }

    /**
     * Load the .env-File from the path
     * @see __construct
     * @return void
     * @see https://dev.to/fadymr/php-create-your-own-php-dotenv-3k2i
     * @todo Implement caching
     */
    public function putEnv(): void
    {
        foreach(self::$configFiles as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            foreach ($lines as $line) {

                if (str_starts_with(trim($line), '#')) {
                    continue;
                }

                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);

                if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                    putenv(sprintf('%s=%s', $name, $value));

                    /** Array? */
                    if(str_contains($value, ',')) {
                        $value = explode(',',$value);
                    }

                    $_ENV[$name] = $value;
                    $_SERVER[$name] = $value;
                }
            }
        }
    }

    /**
     * Getter for fallback (use $_ENV instead)
     * @param string $name
     * @param $default
     * @return mixed
     */
    public static function get(
        string $name,
        $default = null
    ) : mixed
    {
        return $_ENV[$name] ?? $default;
    }

    /**
     * A setter
     * @param string $name
     * @param mixed $value
     * @see putenv()
     * @return void
     */
    public static function set(
        string $name,
        mixed $value
    ): void
    {
        putenv(sprintf('%s=%s', $name, $value));
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }

    /**
     * Check if a variable exists in the environment
     * @param string $name
     * @return bool
     */
    public static function has(
        string $name
    ): bool
    {
        return isset($_ENV[$name]);
    }

    /**
     * Add an additional file to the scope
     * @param string $path
     * @return void
     * @throws Exception
     */
    public static function load(string $path):void {

        if(is_dir($path)) {
            $path .='/.env';
        }

        if(!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('%s does not exist', $path));
        }

        if(file_exists($path)) {
            if(!is_readable($path)) {
                throw new Exception(sprintf('Config File %s is not readable', $path));
            }
        }

        self::$configFiles[] = $path;
    }
}
