<?php

class iogSessionStorage extends sfSessionStorage {

  public function initialize($options = null) {
    // work-around for swfuploader
    if (sfContext::getInstance()->getRequest()->getParameter('swfaction')) {
      session_id(sfContext::getInstance()->getRequest()->getParameter('swfaction'));
    }

    parent::initialize($options);
  }

}
