<?php

class Explanation {
    private $private = '0';
    private $seller_name;
    private $email;
    private $allow_mails = ' ';
    private $phone;
    private $location_id;
    private $category_id;
    private $title;
    private $description;
    private $price;
    
    function __construct(array $exp) {
        $this->private = $exp['private'];
        $this->seller_name = $exp['seller_name'];
        $this->email = $exp['email'];
        $this->allow_mails = $exp['allow_mails'];
        $this->phone = $exp['phone'];
        $this->location_id = $exp['location_id'];
        $this->category_id = $exp['category_id'];
        $this->title = $exp['title'];
        $this->description = $exp['description'];
        $this->price = $exp['price'];
    }

    function get() {
        $exp['private'] = $this->private;
        $exp['seller_name'] = $this->seller_name;
        $exp['email'] = $this->email;
        $exp['allow_mails'] = $this->allow_mails;
        $exp['phone'] = $this->phone;
        $exp['location_id'] = $this->location_id;
        $exp['category_id'] = $this->category_id;
        $exp['title'] = $this->title;
        $exp['description'] = $this->description;
        $exp['price'] = $this->price;
        
        return $exp;
    }
}
