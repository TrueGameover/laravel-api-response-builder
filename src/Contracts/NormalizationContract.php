<?php
declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder\Contracts;

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
interface NormalizationContract
{
    /**
     * Returns array representation of the object.
     *
     * @param object $obj    Object to be converted
     * @param array  $config Converter config array to be used for this object (based on exact class
     *                       name match or inheritance).
     *
     * @return array
     */
    public function convert($obj, array $config): array;

    /**
     * @param $obj Object for check
     * @param array $config
     * @return bool is converter supports $obj
     */
    public function supports($obj, array $config): bool;
}
