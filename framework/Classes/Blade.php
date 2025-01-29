<?php

namespace Framework\Classes;

use eftec\bladeone\BladeOne;
use Parsedown;

class Blade extends BladeOne {

    private static $_instance = null;
    private $parseDown;

    public function __construct() {
        $mode = Config::get('blade.mode');
        $commentMode = Config::get('blade.comment_mode');
        parent::__construct(DIR . '/app/Views', DIR . '/cache/compiled', $mode, $commentMode);

        if (Config::get('blade.pipes')) {
            $this->pipeEnable = true;
        }

        $this->parseDown = new Parsedown();

        $this->directive('csrf', function ($expression) {
            if (empty($expression)) {
                return "<?php echo '<input type=\"hidden\" name=\"_token\" value=\"' . Framework\Classes\Blade::getInstance()->getCsrfToken(true, '_token') . '\">'; ?>";
            }

            $args = explode(',', str_replace(['(', ')', "'"], '', $expression));
            $tokenName = trim($args[0] ?? '_token');
            $fullToken = isset($args[1]) ? trim($args[1]) === 'true' : true;

            return "<?php echo '<input type=\"hidden\" name=\"$tokenName\" value=\"' . Framework\Classes\Blade::getInstance()->getCsrfToken($fullToken, '$tokenName') . '\">'; ?>";
        });

        $this->directive('b', function ($expression) {
            return "<?php echo BASEURL . '/' . $expression; ?>";
        });
    }

    public static function getInstance($templatePath = null, $compiledPath = null, $mode = 0, $commentMode = 0): BladeOne {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function inputToken($fullToken = true, $tokenId = '_token') {
        return "<input type='hidden' name='{$tokenId}' value='" . self::getInstance()->getCsrfToken($fullToken, $tokenId) . "'>";
    }

    public static function view($view, $vars = []) {
        return self::getInstance()->run($view, $vars);
    }

    public function compileMarkdownFile($expression) {
        $args = $this->getArgs($expression);
        $filename = $args['file'];
        $filePath = DIR . '/' . ltrim($filename, '/');

        if (file_exists($filePath)) {
            $markdownContent = file_get_contents($filePath);
            $html = $this->parseDown->text($markdownContent);
            return "<?php echo '$html'; ?>";
        } else {
            return "<?php echo 'Markdown file not found!'; ?>";
        }
    }
}
