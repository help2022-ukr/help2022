<?php

namespace Drupal\h22_core\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\node\Entity\Node;

/**
 * Class ViewContactInformation.
 */
class ViewContactInformation extends FormBase {
  private Node $node;

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
    $form['submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => ['button--primary']
      ],
      '#ajax' => [
        'callback' => '::promptCallback',
        'wrapper' => 'box-container',
      ],
      '#value' => $this->t('View Content Details'),
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
    $element['container']['tel'] = $this->node->field_phone_number->view('teaser');
    $element['container']['telegram'] = $this->node->field_telegram->view('teaser');
    $element['container']['email'] = $this->node->field_email->view('teaser');
    $element['container']['fb'] = $this->node->field_facebook->view('teaser');
    return $element;
  }

}
