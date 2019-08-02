<?php
/**
 *@since 2019.06.27 自定义cdn
 **/
namespace WND\CDN;

class Wnd_CDN extends CDN_Rewrite {

	public static $option;

	function __construct() {

		Wnd_CDN::$option = get_option('wndoss');
		parent::__construct(
			Wnd_CDN::$option['wndoss_site_url'] ?? get_option('siteurl'),
			Wnd_CDN::$option['wndoss_cdn_url'] ?? '',
			Wnd_CDN::$option['wndoss_cdn_dirs'] ?? 'wp-content,wp-includes',
			array_map('trim', explode(',', Wnd_CDN::$option['wndoss_cdn_excludes'] ?? '.php'))
		);

		add_action('template_redirect', array($this, 'register_as_output_buffer'));
	}

	public function register_as_output_buffer() {
		if ($this->blog_url != Wnd_CDN::$option['wndoss_cdn_url']) {
			ob_start(array(&$this, 'rewrite'));
		}
	}

}

class CDN_Rewrite {

	var $blog_url = null;
	var $cdn_url = null;
	var $include_dirs = null;
	var $excludes = array();
	var $rootrelative = false;

	function __construct($blog_url, $cdn_url, $include_dirs, array $excludes, $root_relative = false) {
		$this->blog_url = $blog_url;
		$this->cdn_url = $cdn_url;
		$this->include_dirs = $include_dirs;
		$this->excludes = $excludes;
		$this->rootrelative = $root_relative;
	}

	protected function exclude_single(&$match) {
		foreach ($this->excludes as $badword) {
			if (stristr($match, $badword) != false) {
				return true;
			}
		}
		return false;
	}

	protected function rewrite_single(&$match) {
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

	public function rewrite(&$content) {
		$dirs = $this->include_dirs_to_pattern();
		$regex = '#(?<=[(\"\'])';
		$regex .= $this->rootrelative
		? ('(?:' . quotemeta($this->blog_url) . ')?')
		: quotemeta($this->blog_url);
		$regex .= '/(?:((?:' . $dirs . ')[^\"\')]+)|([^/\"\']+\.[^/\"\')]+))(?=[\"\')])#';
		return preg_replace_callback($regex, array(&$this, 'rewrite_single'), $content);
	}

}
