<?php
declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder\Normalizers;

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

final class JsonSerializableNormalizer implements NormalizationContract
{
    /**
     * Returns array representation of the object implementing \JsonSerializable interface.
     *
     * @param \JsonSerializable $obj Object to be converted
     * @param array $config Converter config array to be used for this object (based on exact class
     *                                  name match or inheritance).
     *
     * @return array
     */
    public function convert($obj, /** @scrutinizer ignore-unused */ array $config): array
    {
        return [\json_decode(json_encode($obj->jsonSerialize()), true)];
    }

    public function supports($obj, array $config): bool
    {
        return $obj instanceof \JsonSerializable;
    }
}
