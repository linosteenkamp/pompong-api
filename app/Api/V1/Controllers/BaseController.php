<?php
/**
 * Created by PhpStorm.
 * User: linosteenkamp
 * Date: 2017/05/01
 * Time: 7:09 PM
 */

namespace pompong\Api\V1\Controllers;

use pompong\Http\Controllers\Controller;
use Dingo\Api\Routing\Helpers;

class BaseController extends Controller
{
    use Helpers;

    protected $url;
    protected $itemsPerPage;

    public function __construct() {
        $this->url = request()->url();
        $this->url = ($this->itemsPerPage ? $this->url . '?itemsPerPage=' .$this->itemsPerPage : $this->url);
        $this->itemsPerPage = request()->get('itemsPerPage');
    }
}