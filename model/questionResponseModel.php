<?php

class QuestionResponseModel {
  public $current_page;
  public $category_id;
  public $question_list;

  function __construct($current_page, $category_id, $question_list) {
    $this->current_page = $current_page;
    $this->category_id = $category_id;
    $this->question_list = $question_list;
  }
}


?>