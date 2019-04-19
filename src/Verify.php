<?php
/**
 * Created by PhpStorm.
 * User: minor
 * Date: 2019/04/18
 * Time: 16:57
 */

namespace Minor\Slide;

use Intervention\Image\Response;

class Verify
{

    /**
     * @var Image
     */
    private $slideImage;
    /**
     * 滑块横坐标
     */
    const SLIDE_X_SESSION_NAME = 'minor_slide_x';
    /**
     * 错误次数
     */
    const SLIDE_ERROR_TIME_SESSION_NAME = 'minor_slide_error_times';

    /**
     * 验证状态
     */
    const SLIDE_VERIFY_STATUS_SESSION_NAME = 'minor_slide_verify_status';

    /**
     * 最大错误次数
     */
    const MAX_ERROR_TIMES = 5;
    /**
     * 误差像素
     */
    const ERRORS = 5;

    /**
     * Verify constructor.
     *
     * @param $originFile
     * @param null $width
     * @param null $height
     * @param null $markSize
     */
    public function __construct($originFile, $width = null, $height = null, $markSize = null)
    {
        $this->slideImage = new Image($originFile, $width, $height, $markSize);
    }

    /**
     * 前端所需要的滑动图片
     *
     * @return Response
     * @date 2019/04/18
     * @author minor
     */
    public function renderImage()
    {
        $imageResponse = $this->slideImage->response();

        $this->saveVerifyParams($this->slideImage->getSlideX());

        return $imageResponse;
    }

    /**
     * 校验前端传的滑动距离
     *
     * @param $slideWidth
     * @return bool
     * @date 2019/04/18
     * @author minor
     * @throws SlideException
     */
    public static function check($slideWidth)
    {
        if (static::getErrorTimes() > static::MAX_ERROR_TIMES || empty(static::getSessionSlideX())) {
            throw new SlideException('Please refresh the slider image!', 4003);
        }

        if (abs(static::getSessionSlideX() - $slideWidth) <= static::ERRORS) {
            static::verifiedSuccess();
            return true;
        }
        static::incrementErrorTimes();

        return false;
    }

    /**
     * 是否验证通过
     *
     * @return bool
     * @date 2019/04/18
     * @author minor
     */
    public static function isVerified()
    {
        return session(static::SLIDE_VERIFY_STATUS_SESSION_NAME);
    }

    /**
     * 验证通过时触发
     *
     * @date 2019/04/18
     * @author minor
     */
    private static function verifiedSuccess()
    {
        session([
            static::SLIDE_VERIFY_STATUS_SESSION_NAME => true,
            static::SLIDE_X_SESSION_NAME => false
        ]);
    }

    /**
     * 保存验证所需参数到 session
     *
     * @param $x
     * @date 2019/04/18
     * @author minor
     */
    private static function saveVerifyParams($x)
    {
        session([
            static::SLIDE_X_SESSION_NAME => $x,
            static::SLIDE_ERROR_TIME_SESSION_NAME => 0
        ]);
    }

    /**
     * 增加验证错误次数
     *
     * @date 2019/04/18
     * @author minor
     */
    private static function incrementErrorTimes()
    {
        $errorTimes = static::getErrorTimes();

        session([
            static::SLIDE_ERROR_TIME_SESSION_NAME => $errorTimes + 1
        ]);
    }

    /**
     * 获取验证错误次数
     *
     * @return int
     * @date 2019/04/18
     * @author minor
     */
    private static function getErrorTimes()
    {
        return session(static::SLIDE_ERROR_TIME_SESSION_NAME);
    }

    /**
     * 获取 session 中存储的所需滑动距离
     *
     * @return mixed
     * @date 2019/04/18
     * @author minor
     */
    private static function getSessionSlideX()
    {
        return session(static::SLIDE_X_SESSION_NAME);
    }
}