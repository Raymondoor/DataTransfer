<?php declare(strict_types=1);
namespace Frrame\View;
class HtmlPresenter{
    /** @var array<string,mixed> Readable in an included view as $this->{$key} via __get(). */
    public mixed $data = [];
    /** @param string $key @param mixed $value */
    public function set(string $key, mixed $value):self{
        $this->data[$key] = $value;
        return $this;
    }
    /**
     * Sets $key if unset, otherwise appends to it (expects $data[$key] to already be an array).
     * @param string $key @param mixed $value
     */
    public function append(string $key, mixed $value):self{
        if(!isset($this->data[$key])){
            $this->data[$key] = $value;
            return $this;
        }
        $this->data[$key][] = $value;
        return $this;
    }
    public function __get(string $key):mixed{
        return $this->data[$key] ?? null;
    }
    public function __isset(string $key):bool{
        return isset($this->data[$key]);
    }
    /** @param string $file Path relative to resource/view/. */
    public function render(string $file):void{
        include(realpath(RESOURCE_PATH.'/view/'.$file));
    }
    /** @param string $file Path relative to resource/view/. */
    public function render_once($file):void{
        include_once(realpath(RESOURCE_PATH.'/view/'.$file));
    }
}