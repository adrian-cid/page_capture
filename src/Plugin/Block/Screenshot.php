<?php
/**
 * @file
 * Contains \Drupal\page_capture\Plugin\Block\Screenshot.
 */
namespace Drupal\page_capture\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\page_capture\Form\ScreenshotForm;


/**
 * Provides a 'Screenshot' block.
 *
 * @Block(
 *   id = "screenshot_block",
 *   admin_label = @Translation("Capture block"),
 *   category = @Translation("Page capture")
 * )
 */
class Screenshot extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $builtForm = \Drupal::formBuilder()->getForm('Drupal\page_capture\Form\ScreenshotForm');
    $render['form'] = $builtForm;

    return $render;
  }
}
