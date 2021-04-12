<?php
/**
 *@since 2019.06.27 自定义cdn
 **/
namespace WPOSS;

class CDN extends CDN_Rewrite {

	protected static $option;

	public function __construct() {
		self::$option = get_option('wndoss');
		parent::__construct(
			self::$option['wndoss_site_url'] ?? get_option('siteurl'),
			self::$option['wndoss_cdn_url'] ?? '',
			self::$option['wndoss_cdn_dirs'] ?? 'wp-content,wp-includes',
			array_map('trim', explode(',', self::$option['wndoss_cdn_excludes'] ?? '.php'))
		);

		add_action('setup_theme', array($this, 'register_as_output_buffer'));
	}

	public function register_as_output_buffer() {
		if ($this->blog_url != self::$option['wndoss_cdn_url']) {
			ob_start(array($this, 'rewrite'));
		}
	}
}

class CDN_Rewrite {

	protected $blog_url     = null;
	protected $cdn_url      = null;
	protected $include_dirs = null;
	protected $excludes     = array();
	protected $rootrelative = false;

	function __construct($blog_url, $cdn_url, $include_dirs, array $excludes, $root_relative = false) {
		$this->blog_url     = $blog_url;
		$this->cdn_url      = $cdn_url;
		$this->include_dirs = $include_dirs;
		$this->excludes     = $excludes;
		$this->rootrelative = $root_relative;
	}

	protected function exclude_single($match) {
		foreach ($this->excludes as $badword) {
			if (stristr($match, $badword) != false) {
				return true;
			}
		}
		return false;
	}

	protected function rewrite_single($match) {
		if ($this->exclude_single($match[0])) {
			return $match[0];
		} else {
			if (!$this->rootrelative || strstr($match[0], $this->blog_url)) {
				return str_replace($this->blog_url, $this->cdn_url, $match[0]);
			} else {
				return $this->cdn_url . $match[0];
			}
		}
	}

	protected function include_dirs_to_pattern() {
		$input = explode(',', $this->include_dirs);
		if ($this->include_dirs == '' || count($input) < 1) {
			return 'wp\-content|wp\-includes';
		} else {
			return implode('|', array_map('quotemeta', array_map('trim', $input)));
		}
	}

	public function rewrite($content) {
		$dirs  = $this->include_dirs_to_pattern();
		$regex = '#(?<=[(\"\'])';
		$regex .= $this->rootrelative
		? ('(?:' . quotemeta($this->blog_url) . ')?')
		: quotemeta($this->blog_url);
		$regex .= '/(?:((?:' . $dirs . ')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';
		return preg_replace_callback($regex, array($this, 'rewrite_single'), $content);
	}
}
