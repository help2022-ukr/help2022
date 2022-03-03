<?php

namespace Drupal\h22_core\Form;

use Drupal\Core\Flood\FloodInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\flag\FlagLinkBuilderInterface;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ViewContactInformation.
 */
class ViewContactInformation extends FormBase {
  private Node $node;

  /**
   * The flood service.
   *
   * @var \Drupal\Core\Flood\FloodInterface
   */
  protected $flood;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The flag builder service.
   *
   * @var \Drupal\flag\FlagLinkBuilderInterface
   */
  protected $flagService;

  public function __construct(FloodInterface $flood, RequestStack $requestStack, FlagLinkBuilderInterface $flagLinkBuilder) {
    $this->flood = $flood;
    $this->requestStack = $requestStack->getCurrentRequest();
    $this->flagService = $flagLinkBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('flood'),
      $container->get('request_stack'),
      $container->get('flag.link_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'view_contact_information';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, Node $node = NULL) {
    $form['#attached']['library'][] = 'h22_core/contact_details';

    $form['submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => ['button--primary']
      ],
      '#ajax' => [
        'callback' => '::promptCallback',
        'wrapper' => 'box-container',
      ],
      '#value' => $this->t('View Contact Details'),
      '#prefix' => '<div id="box-container">',
      '#suffix' => '</div>'
    ];
    if (!empty($node)) {
      $this->node = $node;
    }
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      \Drupal::messenger()->addMessage($key . ': ' . ($key === 'text_format'?$value['value']:$value));
    }

    // User flood control.
    $flood_identifier = $this->requestStack->getClientIp();
    $this->flood->register('h22_core.view_contact_information', 3600, $flood_identifier);
  }

  /**
   * Callback for submit_driven example.
   *
   * Select the 'box' element, change the markup in it, and return it as a
   * renderable array.
   *
   * @return array
   *   Renderable array (the box element)
   */
  public function promptCallback(array &$form, FormStateInterface $form_state) {
    $element['container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'contacts-wrapper'
      ]
    ];

    // User flood control validation.
    $flood_identifier = $this->requestStack->getClientIp();
    if (!$this->flood->isAllowed('h22_core.view_contact_information', 30, 1800, $flood_identifier)) {
      $element['container']['message'] = [
        '#type' => 'item',
        '#markup' => $this->t("You have viewed too many contacts. There's a protection against hackers - please try again later."),
        '#wrapper_attributes' => ['class' => ['messages-list__item messages messages--error']],
        '#weight' => '0',
      ];
    }
  else {
    $element['container']['tel'] = $this->node->field_phone_number->view('teaser');
    $element['container']['telegram'] = $this->node->field_telegram->view('teaser');
    $element['container']['email'] = $this->node->field_email->view('teaser');
    $element['container']['fb'] = $this->node->field_facebook->view('teaser');

    $flag = $this->flagService->build('node', $this->node->id(), 'host_location_report_flag');

    $element['container']['flag'] = $flag;
    $element['container']['flag']['#attributes']['class'][] = 'button button--danger';
    $element['container']['flag']['#weight'] = 9;
    }
    return $element;
  }

}
