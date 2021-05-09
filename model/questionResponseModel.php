<?php

class QuestionResponseModel {
  public $pages;
  public $current_page;
  public $category_id;
  public $question_list;

  function __construct($pages, $current_page, $category_id, $question_list) {
    $this->pages = $pages;
    $this->current_page = $current_page;
    $this->category_id = $category_id;
    $this->question_list = $question_list;
  }
}


?>