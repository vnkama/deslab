<?php

namespace controllers;


class CommentsController extends \core\Controller
{
    function routeGET()
    {
        $this->data2View['title']       = 'Комментарии';
        $this->data2View['body_html']   = 'index.html';
        $this->data2View['body_class']  = 'p01';


        $this->runModelView('template.html','CommentsModel');
    }
}
