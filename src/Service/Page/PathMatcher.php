<?php

/**
 * @file
 * Contains \Drupal\acquia_lift\Service\Page\PathMatcher.
 */

namespace Drupal\acquia_lift\Service\Page;

use Drupal\Core\Path\AliasManager;
use Drupal\Core\Path\PathMatcher as BasePathMatcher;
use Drupal\Component\Utility\Unicode;

class PathMatcher {
  /**
   * Alias manager.
   *
   * @var \Drupal\Core\Path\AliasManager
   */
  private $aliasManager;

  /**
   * Path matcher.
   *
   * @var \Drupal\Core\Path\PathMatcher
   */
  private $pathMatcher;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Path\AliasManager $alias_manager
   *   The alias manager service.
   * @param \Drupal\Core\Path\PathMatcher $path_matcher
   *   The path matcher service.
   */
  public function __construct(AliasManager $alias_manager, BasePathMatcher $path_matcher) {
    $this->aliasManager = $alias_manager;
    $this->pathMatcher = $path_matcher;
  }

  /**
   * Determine if the path falls into one of the allowed paths (in terms of path patterns).
   *
   * @todo: $path needs to be able to take alias (and verify its path), as well.
   *
   * @param string $path
   *   The actual path that's being matched by.
   * @param string $path_patterns
   *   The path patterns that the path is being matched to.
   *
   * @return boolean
   *   True if should attach.
   */
  public function match($path, $path_patterns) {
    // Convert path to lowercase and match.
    $converted_path = Unicode::strtolower($path);
    $converted_path_patterns = Unicode::strtolower($path_patterns);
    if ($this->pathMatcher->matchPath($converted_path, $converted_path_patterns)) {
      return TRUE;
    }

    // Compare the lowercase path alias (if any) and internal path.
    $converted_path_alias = Unicode::strtolower($this->aliasManager->getAliasByPath($converted_path));
    if (($converted_path != $converted_path_alias) && $this->pathMatcher->matchPath($converted_path_alias, $converted_path_patterns)) {
      return TRUE;
    }

    return FALSE;
  }
}
