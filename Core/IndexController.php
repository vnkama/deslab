<?php

namespace Core;

//use Core\Controller;

class IndexController extends Controller
{
    function run()
    {
        $this->data2View['title']       = 'Комментарии';
        $this->data2View['body_html']   = 'index.html';
        $this->data2View['body_class']  = 'p01';


        $this->runModelView('template.html','CommentsModel');
    }


}
