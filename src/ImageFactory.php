<?php
/**
 * Created by PhpStorm.
 * User: ycz
 * Date: 2019/04/19
 * Time: 11:59
 */

namespace Minor\Slide;


use Intervention\Image\ImageManagerStatic;

/**
 * Class ImageFactory
 *
 * @package Minor\Slide
 * @method static \Intervention\Image\Image  canvas(Int $width, Int $height, Mixed $background = null)
 * @method static \Intervention\Image\Image  make(Mixed $data)
 * @method static \Intervention\Image\Image  cache(\Closure $callback, $lifetime = null, $returnObj = false)
 */
class ImageFactory
{
    private static $configureStatus = false;

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @date 2019/04/19
     * @author ycz
     */
    public static function __callStatic($name, $arguments)
    {
        if (!static::$configureStatus) {
            ImageManagerStatic::configure(static::getConfig());
            static::$configureStatus = true;
        }

        return ImageManagerStatic::$name(...$arguments);
    }


    /**
     * @return array
     * @date 2019/04/19
     * @author ycz
     */
    protected static function getConfig()
    {
        if (function_exists('config') && is_array(config('image'))) {
            return config('image');
        }

        return ['driver' => 'gd'];
    }
}