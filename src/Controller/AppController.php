<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;

class AppController extends Controller {
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('FormProtection');`
     *
     * @return void
     */
  public function initialize(): void {
      parent::initialize();
      $this->loadComponent('RequestHandler');
      $this->loadComponent('Flash');
      //add for authentication
      $this->loadComponent('Authentication.Authentication');
      //add for authorization
      $this->loadComponent('Authorization.Authorization');
  }

  public function beforeFilter(\Cake\Event\EventInterface $event) {
      parent::beforeFilter($event);
      // for all controllers in our application, make index and view
      // actions public, skipping the authentication check
      $this->Authentication->addUnauthenticatedActions(['index', 'view']);
  }
}
