<?php

namespace Drupal\page_capture\Form;

use Drupal\Core\Form\FormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\phantomjs_capture\PhantomJSCaptureHelperInterface;
use Drupal\Core\Url;

/**
 * Class ScreenshotForm
 *
 * Provide a button to Capture the screen.
 *
 * @package Drupal\page_capture\Form
 */
class ScreenshotForm extends FormBase {

  /**
   * @var PhantomJSCaptureHelper
   */
  private $captureHelper;

  /**
   * ScreenshotForm constructor.
   * @param PhantomJSCaptureHelperInterface $phantomjs_capture_helper
   */
  public function __construct(PhantomJSCaptureHelperInterface $phantomjs_capture_helper) {
    $this->captureHelper = $phantomjs_capture_helper;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('phantomjs_capture.helper'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'page_capture';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $current_path = \Drupal::service('path.current')->getPath();
    $path_alias = \Drupal::service('path.alias_manager')->getAliasByPath($current_path);
    $current_url = \Drupal\Core\Url::fromUserInput($path_alias, array('absolute' => TRUE))->toString();

    $form['url'] = array(
      '#type' => 'hidden',
      '#default_value' => $current_url,
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Capture'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('phantomjs_capture.settings');
    $values = $form_state->getValues();
    $url = Url::fromUri($values['url']);

    $file = 'capture_test' . '.png';
    $destination = \Drupal::config('system.file')->get('default_scheme') . '://' . $config->get('destination') . '/test/' . REQUEST_TIME;
    $file_url = file_create_url($destination . '/' . $file);

    if ($this->captureHelper->capture($url, $destination, $file)) {
      drupal_set_message($this->t('The file has been generated! You can view it <a href=":url" target="_blank">here</a>', array(':url' => $file_url)));
    } else {
      drupal_set_message('The address entered could not be retrieved, directory was not writeable, or phantomjs could not perform the action requested.', 'error');
    }
  }
}
