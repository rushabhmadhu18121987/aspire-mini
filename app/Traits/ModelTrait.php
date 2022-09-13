<?php

namespace App\Traits;

use Request, Validator;

trait ModelTrait
{
    //Set created_at
    public function set_createdAt() {
        $this->created_at = time();
    }
    
    //Set updated_at
    public function set_updatedAt() {
        $this->updated_at = time();
    }

    //Get Auto incremented ID of record
    public function getId() {
        return $this->id;
    }

    public function get_createdAt() {
        return date('Y-m-d', $this->created_at);
    }
}