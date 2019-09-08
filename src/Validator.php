<?php
declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder;

/**
 * Laravel API Response Builder
 *
 * @package   MarcinOrlowski\ResponseBuilder
 *
 * @author    Marcin Orlowski <mail (#) marcinorlowski (.) com>
 * @copyright 2016-2019 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/laravel-api-response-builder
 */

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;


class Validator
{
	/**
	 * @param string $key
	 * @param mixed  $var
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function assertInt(string $key, $var): void
	{

		if (!is_int($var)) {
			$msg = sprintf('"%s" must be an integer (%s given)', $key, gettype($var));
			throw new \InvalidArgumentException($msg);
		}
	}

	/**
	 * @param string $key
	 * @param mixed  $var
	 * @param int    $min
	 * @param int    $max
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function assertIntRange(string $key, $var, int $min, int $max): void
	{
		self::assertInt($key, $var);

		if (($min > $var) && ($var > $max)) {
			throw new \InvalidArgumentException(
				sprintf('Invalid value of "%s" (%d). Must be between %d-%d inclusive', $key, $var, $min, $max));
		}
	}

	/**
	 * @param string $key
	 * @param mixed  $var
	 *
	 * @throws \InvalidArgumentException
	 */
	public static function assertNull(string $key, $var): void
	{
		if ($var !== null) {
			$msg = sprintf('"%s" must be null (%s given)', $key, gettype($var));
			throw new \InvalidArgumentException($msg);
		}
	}
}
