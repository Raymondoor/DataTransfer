<?php declare(strict_types=1);
namespace Frrame\View\Context;
final class PageContext{
	/** @var array<string,mixed> */
	public $page = [
		'TITLE' => 'Title',
		'INDEX' => 'index',
		'ALIAS' => 'Alias'
	];
	/** @param array<string,mixed> $page */
	public function __construct(array $page = []){
		if($page !== []) $this->set($page);
	}
	/**
	 * @param array<string,mixed> $page
	 * @return array<string,mixed>
	 */
	public function set(array $page):mixed{
		$this->page = array_merge($this->page, $page);
		return $this->page;
	}
	public function get(string $key):mixed{
		return $this->page[$key];
	}
	public function indexIs(string $index=''):bool{
		return $this->get('INDEX') === $index;
	}
	public function indexAre(string $index=''):bool{
		return str_starts_with($this->get('INDEX'), $index);
	}
	public function title(?string $title = null):string{
		if(!is_null($title)){
			$this->set(['TITLE' => $title]);
		}
		return $this->get('TITLE');
	}
	public function index(?string $index = null):string{
		if(!is_null($index)){
			$this->set(['INDEX' => $index]);
		}
		return $this->get('INDEX');
	}
}