<?php
declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder\Tests\Converters;

/**
 * Laravel API Response Builder
 *
 * @package   MarcinOrlowski\ResponseBuilder
 *
 * @author    Marcin Orlowski <mail (#) marcinOrlowski (.) com>
 * @copyright 2016-2020 Marcin Orlowski
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      https://github.com/MarcinOrlowski/laravel-api-response-builder
 */

use MarcinOrlowski\ResponseBuilder\Contracts\NormalizationContract;

class FakeNormalization implements NormalizationContract
{
    public $key = 'fake';
    public $val = 'converter';

    public function convert($obj, /** @scrutinizer ignore-unused */ array $config): array
    {
        return [$this->key => $this->val];
    }

    public function supports($obj, array $config): bool
    {
        return true;
    }
}
