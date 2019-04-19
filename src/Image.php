<?php
/**
 * Created by PhpStorm.
 * User: minor
 * Date: 2019/04/18
 * Time: 15:31
 */

namespace Minor\Slide;


class Image
{

    private $slideX;

    private $slideY;

    private $originFile;

    private $width = 240;

    private $height = 150;

    private $markSize = 40;
    /**
     * @var \Intervention\Image\Image
     */
    private $backgroundFile;
    /**
     * @var \Intervention\Image\Image
     */
    private $slideFile;

    /**
     * Image constructor.
     *
     * @param null $originFile
     * @param null $width
     * @param null $height
     * @param null $markSize
     */
    public function __construct($originFile = null, $width = null, $height = null, $markSize = null)
    {
        $this->originFile = $originFile && file_exists($originFile) ? $originFile : $this->getDefaultBackground();
        $this->width = $width > $this->width ? $width : $this->width;
        $this->height = $height > $this->height ? $height : $this->height;
        $this->markSize = $markSize > $this->markSize ? $markSize : $this->markSize;

        if ($this->height < $this->markSize + 1) {
            $this->markSize = round($this->height / 2);
        }

        if ($this->width < $this->markSize + 1) {
            $this->markSize = round($this->width / 2);
        }
    }

    /**
     * @return mixed
     * @date 2019/04/18
     * @author minor
     */
    public function response()
    {
        $this->generateSlideFile();
        $this->generateBackgroundFile();

        return $this->merge()->response('png');
    }

    /**
     * @return int
     * @date 2019/04/18
     * @author minor
     */
    public function getSlideX()
    {
        return $this->slideX;
    }

    /**
     * @return \Intervention\Image\Image
     * @date 2019/04/18
     * @author minor
     */
    private function merge()
    {

        $canvas = ImageFactory::canvas($this->width, $this->height * 2);

        $canvas->insert($this->backgroundFile, 'top');

        $canvas->insert($this->slideFile, 'bottom');

        return $canvas;
    }

    /**
     * @return \Intervention\Image\Image
     * @date 2019/04/18
     * @author minor
     */
    private function getResizedOriginFile()
    {
        return ImageFactory::make($this->originFile)->resize($this->width, $this->height);
    }

    /**
     * @date 2019/04/18
     * @author minor
     */
    private function generateSlideFile()
    {
        $canvas = ImageFactory::canvas($this->width, $this->height);

        $canvas->opacity(0);
        $this->slideFile = $canvas->insert(
            $this->getResizedOriginFile()->crop(
                $this->markSize, $this->markSize, $this->generateSlideX(), $this->generateSlideY()
            ), '', 0, $this->getSlideY());
    }

    /**
     * @date 2019/04/18
     * @author minor
     */
    private function generateBackgroundFile()
    {
        $mark = ImageFactory::canvas($this->markSize, $this->markSize, '#ccc');

        $this->backgroundFile = $this->getResizedOriginFile()->insert($mark, '', $this->getSlideX(), $this->getSlideY());
    }


    /**
     * @return int
     * @date 2019/04/18
     * @author minor
     */
    private function generateSlideX()
    {
        $this->slideX = mt_rand(50, $this->width - $this->markSize - 1);

        return $this->slideX;
    }

    /**
     * @return int
     * @date 2019/04/18
     * @author minor
     */
    private function generateSlideY()
    {
        $this->slideY = mt_rand(0, $this->height - $this->markSize - 1);

        return $this->slideY;
    }


    /**
     * @return string
     * @date 2019/04/18
     * @author minor
     */
    private function getDefaultBackground()
    {
        return __METHOD__ . 'example.jpg';
    }

    /**
     * @return int
     * @date 2019/04/18
     * @author minor
     */
    private function getSlideY()
    {
        return $this->slideY;
    }
}