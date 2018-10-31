<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EuctOrder extends Model
{
  const CREATED_AT = 'CREATE_DT';
  const UPDATED_AT = 'UPDATE_DT';

  //public $timestamps = false;
  protected $table = 'EUCT_ORDER';
}
