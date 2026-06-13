<?php

namespace ELKLab\ElkAnalytics\Helpers;

use ELKLab\ElkAnalytics\Helpers\Blade;

/**
 * Template Helper
 * 
 * @since 1.0.0
 */
class TemplateHelper {
  /**
   * Blade instance
   *
   * @var Blade
   */
  protected static $blade;

  /**
   * Initialize Blade
   * 
   * @return void
   * @since 1.0.0
   */
  public static function init() {
    if (!self::$blade) {
      $cache = ELK_ANALYTICS_CACHE_DIR;
      if (!file_exists($cache)) {
        mkdir($cache, 0755, true);
      }
      self::$blade = new Blade([], $cache);
    }
  }

  /**
   * Render a template
   * 
   * @param string $template
   * @param array $data
   * @return string
   * @since 1.0.0
   */ 
  public static function render($template, $data = []) {
    self::init();

    $template = str_replace('.php', '', $template);
    $template = str_replace('.', '/', $template);

    $themeBlade = get_template_directory() . '/elk-analytics/' . $template . '.blade.php';
    $themePhp = get_template_directory() . '/elk-analytics/' . $template . '.php';
    $pluginBlade = ELK_ANALYTICS_PLUGIN_DIR . 'templates/' . $template . '.blade.php';
    $pluginPhp = ELK_ANALYTICS_PLUGIN_DIR . 'templates/' . $template . '.php';

    if (file_exists($themeBlade)) {
      self::$blade = new Blade([get_template_directory() . '/elk-analytics'], ELK_ANALYTICS_CACHE_DIR);
      return self::$blade->make($template, $data)->render();
    } elseif (file_exists($themePhp)) {
      extract($data);
      include $themePhp;
    } elseif (file_exists($pluginBlade)) {
      self::$blade = new Blade([ELK_ANALYTICS_PLUGIN_TEMPLATES_DIR], ELK_ANALYTICS_CACHE_DIR);
      return self::$blade->make($template, $data)->render();
    } elseif (file_exists($pluginPhp)) {
      extract($data);
      include $pluginPhp;
    } else {
      return __('Template not found', 'elk-analytics');
    }
  }
}
