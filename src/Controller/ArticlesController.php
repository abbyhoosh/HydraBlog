<?php
// src/Controller/ArticlesController.php

namespace App\Controller;

class ArticlesController extends AppController {

  /**
   * Index method
   *
   * @return \Cake\Http\Response|null|void renders view.
   */
  public function index() {
      $this->Authorization->skipAuthorization();
      $this->loadComponent('Paginator');
      $articles = $this->Paginator->paginate($this->Articles->find());
      $this->set(compact('articles'));
  }

  /**
    * View method
    *
    * Displays a specific article
    * @param string $slug Article slug
    * @return \Cake\Http\Response|null|void renders view otherwise.
    */
  public function view($slug) {
      $this->Authorization->skipAuthorization();
      $article = $this->Articles
        ->findBySlug($slug)
        ->contain('Tags')
        ->firstOrFail();
      $this->set(compact('article'));
  }

  /**
    * Add method
    *
    * creates a new article
    * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
    */
  public function add() {
      $article = $this->Articles->newEmptyEntity();
      $this->Authorization->authorize($article);
    
      if ($this->request->is('post')) {
          $article = $this->Articles->patchEntity($article, $this->request->getData());
          $article->user_id = $this->request->getAttribute('identity')->getIdentifier();
        
          if ($this->Articles->save($article)) {
              $this->Flash->success(__('Your article has been saved.'));
              return $this->redirect(['action' => 'index']);
          }
          $this->Flash->error(__('Unable to add your article.'));
      }
      // Get a list of tags.
      $tags = $this->Articles->Tags->find('list')->all();
      // Set tags to the view context
      $this->set('tags', $tags);
      $this->set('article', $article);
  }

  /**
    * Edit method
    *
    * Edits an existing article
    * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
    */
  public function edit($slug) {
      $article = $this->Articles
        ->findBySlug($slug)
        ->contain('Tags') // load associated Tags
        ->firstOrFail();
      $this->Authorization->authorize($article);

      if ($this->request->is(['post', 'put'])) {
          //Disable modification of user_id.
          $this->Articles->patchEntity($article, $this->request->getData(), ['accessibleFields' => ['user_id' => false]]);
          if ($this->Articles->save($article)) {
              $this->Flash->success(__('Your article has been updated.'));
              return $this->redirect(['action' => 'index']);
          }
          $this->Flash->error(__('Unable to update your article.'));
      }
      //find list of tags
      $tags = $this->Articles->Tags->find('list')->all();
      //set tags to view context
      $this->set(compact('article', 'tags'));
  }

   /**
    * Delete method
    *
    * Deltes an existing article
    * @return \Cake\Http\Response|null|void Redirects on successful delete, renders view otherwise.
    */
  public function delete($slug) {
      $this->request->allowMethod(['post', 'delete']);
      $article = $this->Articles->findBySlug($slug)->firstOrFail();
      $this->Authorization->authorize($article);
    
      if ($this->Articles->delete($article)) {
          $this->Flash->success(__('The {0} article has been deleted.', $article->title));
          return $this->redirect(['action' => 'index']);
      }
  }

  /**
    * Tags method
    *
    * Display articles with the tags searched for
    * @return \Cake\Http\Response|null|void renders view
    */
  public function tags() {
    // The 'pass' key is provided by CakePHP and contains all
    // the passed URL path segments in the request.
    $tags = $this->request->getParam('pass');
    $this->Authorization->skipAuthorization();
    // Use the ArticlesTable to find tagged articles.
    $articles = $this->Articles->find('tagged', ['tags' => $tags]) ->all();
    $this->set(['articles' => $articles, 'tags' => $tags]);
  }
}