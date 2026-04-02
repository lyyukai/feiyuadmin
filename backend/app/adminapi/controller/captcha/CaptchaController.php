<?php
/**
 * 飞鱼后台管理系统 - 验证码控制器
 */

declare(strict_types=1);

namespace app\adminapi\controller\captcha;

use app\adminapi\controller\BaseAdminController;
use app\service\CaptchaService;
use think\Response;

/**
 * 验证码控制器
 * Class CaptchaController
 */
class CaptchaController extends BaseAdminController
{
    /** @var array 免登录接口 */
    protected array $notNeedLogin = ['generate', 'verify'];

    /**
     * 生成验证码
     * GET /adminapi/captcha/generate
     */
    public function generate(): Response
    {
        $key = $this->param('key', 'login');
        $captcha = CaptchaService::generate($key);

        // 生成图片
        $image = $this->createImageCode($captcha['code']);

        return $this->data([
            'key' => $key,
            'expire' => $captcha['expire'],
            'image' => 'data:image/png;base64,' . base64_encode($image),
        ]);
    }

    /**
     * 验证验证码
     * POST /adminapi/captcha/verify
     */
    public function verify(): Response
    {
        $code = $this->param('code', '');
        $key = $this->param('key', 'login');

        if (empty($code)) {
            return $this->fail('请输入验证码');
        }

        $valid = CaptchaService::verify($code, $key);

        if (!$valid) {
            return $this->fail('验证码错误或已过期');
        }

        return $this->success('验证成功');
    }

    /**
     * 生成验证码图片（GD库）
     */
    protected function createImageCode(string $code): string
    {
        $width = 120;
        $height = 40;
        $fontSize = 22;

        $image = imagecreate($width, $height);
        $bgColor = imagecolorallocate($image, 243, 247, 251); // 浅灰蓝背景
        $textColor = imagecolorallocate($image, 34, 82, 165); // 深蓝色

        // 干扰线
        for ($i = 0; $i < 3; $i++) {
            $lineColor = imagecolorallocate($image, mt_rand(150, 220), mt_rand(150, 220), mt_rand(150, 220));
            imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $lineColor);
        }

        // 验证码文字（居中）
        $fontFile = $this->getFontFile();
        $bbox = imagettfbbox($fontSize, 0, $fontFile, $code);
        $textWidth = $bbox[2] - $bbox[0];
        $x = ($width - $textWidth) / 2;
        $bbox2 = imagettfbbox($fontSize, 0, $fontFile, $code);
        $y = ($height + $bbox2[1] - $bbox2[7]) / 2;

        // 文字描边（抗锯齿）
        imagettftext($image, $fontSize, 0, (int)$x + 1, (int)$y + 1, imagecolorallocate($image, 255, 255, 255), $fontFile, $code);
        imagettftext($image, $fontSize, 0, (int)$x, (int)$y, $textColor, $fontFile, $code);

        // 噪点
        for ($i = 0; $i < 50; $i++) {
            imagesetpixel($image, mt_rand(0, $width), mt_rand(0, $height), imagecolorallocate($image, mt_rand(150, 220), mt_rand(150, 220), mt_rand(150, 220)));
        }

        ob_start();
        imagepng($image);
        $png = ob_get_clean();
        imagedestroy($image);

        return $png;
    }

    /**
     * 获取字体文件（优先使用系统自带）
     */
    protected function getFontFile(): string
    {
        // 尝试常见字体路径
        $fonts = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/truetype/freefont/FreeSansBold.ttf',
        ];

        foreach ($fonts as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }

        // 找不到字体文件时，用内置点阵
        throw new \RuntimeException('验证码字体文件未安装，请执行: apt install fonts-dejavu-core');
    }
}
