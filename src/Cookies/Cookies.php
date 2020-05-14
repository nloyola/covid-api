<?php

namespace App\Cookies;

use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\FigRequestCookies;
use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\SetCookie;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


class Cookies {

  protected $req;
  protected $res;

  public function __construct(ServerRequestInterface &$req, ResponseInterface &$res) {
    $this->req = &$req;
    $this->res = &$res;
  }

  function get($name) {
    return FigRequestCookies::get($this->req, $name, '');
  }

  function set($name, $value = "", $expire = 0) {
    return FigResponseCookies::set($this->res,
                                   SetCookie::create($name)->withValue($value)->withPath('/'));
  }
}
