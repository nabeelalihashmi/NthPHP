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

        // Initialize Parsedown for Markdown parsing
        $this->parseDown = new Parsedown();
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
