<?php
declare(strict_types=1);

namespace MarcinOrlowski\ResponseBuilder;

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

use Illuminate\Support\Facades\Config;
use MarcinOrlowski\ResponseBuilder\Contracts\NormalizationContract;


/**
 * Data converter
 */
class Converter
{
    /**
     * @var NormalizationContract[]
     */
    protected $classes = [];

    /**
     * Converter constructor.
     *
     * @throws \RuntimeException
     */
    public function __construct()
    {
        $this->classes = static::getClassesMapping() ?? [];
    }

    /**
     * Returns local copy of configuration mapping for the classes.
     *
     * @return array
     */
    public function getClasses(): array
    {
        return $this->classes;
    }

    /**
     * Checks if we have "classes" mapping configured for $data object class.
     * Returns @true if there's valid config for this class.
     * Throws \RuntimeException if there's no config "classes" mapping entry for this object configured.
     * Throws \InvalidArgumentException if No data conversion mapping configured for given class.
     *
     * @param object $data Object to check mapping for.
     * @param array $config Context configuration
     *
     * @return NormalizationContract
     *
     */
    protected function getClassClassConverterOrThrow(object $data, array $config): NormalizationContract
    {
        $result = null;

        // check for exact class name match...
        $cls = \get_class($data);
        foreach ($this->classes as $converter) {
            if ($converter->supports($data, $config)) {
                $result = $converter;
                break;
            }
        }

        if ($result === null) {
            throw new \InvalidArgumentException(sprintf('No data conversion mapping configured for "%s" class.', $cls));
        }

        return $result;
    }

    /**
     * We need to prepare source data
     *
     * @param object|array|null $data
     *
     * @param array $config
     * @return array|null
     *
     */
    public function convert($data = null, array $config = []): ?array
    {
        if ($data === null) {
            return null;
        }

        Validator::assertIsType('data', $data, [Validator::TYPE_ARRAY,
            Validator::TYPE_OBJECT]);

        if (\is_object($data)) {
            $converter = $this->getClassClassConverterOrThrow($data, $config);
            $data = $converter->convert($data, $config);
        }

        return $this->convertArray($data); // recursive converting
    }

    /**
     * Recursively walks $data array and converts all known objects if found. Note
     * $data array is passed by reference so source $data array may be modified.
     *
     * @param array $data array to recursively convert known elements of
     *
     * @param array $config
     * @return array
     *
     */
    protected function convertArray(array $data, array $config = []): array
    {
        // This is to ensure that we either have array with user provided keys i.e. ['foo'=>'bar'], which will then
        // be turned into JSON object or array without user specified keys (['bar']) which we would return as JSON
        // array. But you can't mix these two as the final JSON would not produce predictable results.
        $string_keys_cnt = 0;
        $int_keys_cnt = 0;
        foreach ($data as $key => $val) {
            if (\is_int($key)) {
                $int_keys_cnt++;
            } else {
                $string_keys_cnt++;
            }

            if (($string_keys_cnt > 0) && ($int_keys_cnt > 0)) {
                throw new \RuntimeException(
                    'Invalid data array. Either set own keys for all the items or do not specify any keys at all. ' .
                    'Arrays with mixed keys are not supported by design.');
            }
        }

        foreach ($data as $key => $val) {
            if (\is_array($val)) {
                $data[$key] = $this->convertArray($val);
            } elseif (\is_object($val)) {
                $converter = $this->getClassClassConverterOrThrow($val, $config);
                $data[$key] = $converter->convert($val, $config);
            }
        }

        return $data;
    }

    /**
     * Reads and validates "classes" config mapping
     *
     * @return array Classes mapping as specified in configuration or empty array if configuration found
     *
     * @throws \RuntimeException if "classes" mapping is technically invalid (i.e. not array etc).
     */
    protected static function getClassesMapping(): array
    {
        $classes = Config::get(ResponseBuilder::CONF_KEY_CONVERTER);
        $converters = [];

        if ($classes !== null) {
            if (!\is_array($classes)) {
                throw new \RuntimeException(
                    \sprintf('CONFIG: "classes" mapping must be an array (%s given)', \gettype($classes)));
            }

            usort($classes, function (array $first, array $second) {
                if ($first['priority'] > $second['priority']) {
                    return -1;
                }

                if ($first['priority'] < $second['priority']) {
                    return 1;
                }

                return 0;
            });

            foreach ($classes as $class_config) {
                $converter = new $class_config[ResponseBuilder::KEY_HANDLER];

                if ($converter instanceof NormalizationContract) {
                    $converters[] = $converter;
                }
            }
        }

        return $converters;
    }
}
