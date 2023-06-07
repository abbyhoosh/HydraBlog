<?php
declare(strict_types=1);

namespace App\Controller;

class UsersController extends AppController {

  /**
   * Index method
   *
   * @return \Cake\Http\Response|null|void renders view.
   */
  public function index() {
      $this->Authorization->skipAuthorization();
      $users = $this->paginate($this->Users);
      $this->set(compact('users'));
  }

  /**
   * View method
   *
   * Displays a specific user information
   * @param string $id User id
   * @return \Cake\Http\Response|null|void renders view otherwise.
   */  
  public function view($id = null) {
      $this->Authorization->skipAuthorization();
      $user = $this->Users->get($id, ['contain' => ['Articles'],]);
      $this->set(compact('user'));
  }
  
  /**
    * Add method
    *
    * creates a new user
    * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
  */
  public function add() {
      $this->Authorization->skipAuthorization();
      $user = $this->Users->newEmptyEntity();
    
      if ($this->request->is('post')) {
          $user = $this->Users->patchEntity($user, $this->request->getData());
        
          if ($this->Users->save($user)) {
              $this->Flash->success(__('The user has been saved.'));
              return $this->redirect(['action' => 'index']);
          }
          $this->Flash->error(__('The user could not be saved. Please, try again.'));
      }
      $this->set(compact('user'));
  }

 /**
  * Edit method
  *
  * Edits an existing user
  * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
  */
  public function edit($id = null) {
      $user = $this->Users->get($id, ['contain' => [],]);
    
      if ($this->request->is(['patch', 'post', 'put'])) {
          $user = $this->Users->patchEntity($user, $this->request->getData());
        
          if ($this->Users->save($user)) {
              $this->Flash->success(__('The user has been saved.'));
              return $this->redirect(['action' => 'index']);
          }
          $this->Flash->error(__('The user could not be saved. Please, try again.'));
      }
      $this->set(compact('user'));
  }
  
  /**
    * Delete method
    *
    * Deletes an existing user
    * @return \Cake\Http\Response|null|void Redirects on successful delete, renders view otherwise.
    */
  public function delete($id = null) {
      $this->request->allowMethod(['post', 'delete']);
      $user = $this->Users->get($id);
    
      if ($this->Users->delete($user)) {
          $this->Flash->success(__('The user has been deleted.'));
        } else {
          $this->Flash->error(__('The user could not be deleted. Please, try again.'));
      }
    
      return $this->redirect(['action' => 'index']);
  }

  /**
  * beforeFilter method
  *
  * @param \Cake\Event\EventInterface $event
  * @return void
  */
  public function beforeFilter(\Cake\Event\EventInterface $event) {
      parent::beforeFilter($event);
      // Configure the login action to not require authentication
      $this->Authentication->addUnauthenticatedActions(['login']); 
      //allows users to be added
      $this->Authentication->addUnauthenticatedActions(['login', 'add']);
  }

  /**
  *login method
  *
  * find if user trying to login is an existing user
  * 
  * @return \Cake\Http\Response|null|void Redirects on successful login to articles index, renders view otherwise.
  * @throws \Cake\Datasource\Exception\UnauthorizedException When user not found.
  */
  public function login() {
      $this->Authorization->skipAuthorization();
      $this->request->allowMethod(['get', 'post']);
      $result = $this->Authentication->getResult();
      // regardless of POST or GET, redirect if user is logged in
      if ($result && $result->isValid()) {
          // redirect to /articles after login success
          $redirect = $this->request->getQuery('redirect', [
                                               'controller' => 'Articles',
                                               'action' => 'index',]);
      return $this->redirect($redirect);
      }
      // display error if user submitted and authentication failed
      if ($this->request->is('post') && !$result->isValid()) {
          $this->Flash->error(__('Invalid username or password'));
      }
  }

  /**
  * logout method
  *
  * @return redirect to login page
  */
  public function logout() {
      $this->Authorization->skipAuthorization();
      $result = $this->Authentication->getResult();
      // regardless of POST or GET, redirect if user is logged in
      if ($result && $result->isValid()) {
          $this->Authentication->logout();
          return $this->redirect(['controller' => 'Users', 'action' => 'login']);
      }
  }
}
