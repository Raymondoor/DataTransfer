<?php declare(strict_types=1);
namespace Frrame\Controller;
use Frrame\Base\Controller;
use Frrame\Component\Http\RequestMethod;
use Frrame\Component\Http\Request;
use Frrame\Component\I18n;
use Frrame\View\Context\PageContext;
use Frrame\View\HtmlPresenter;
class WelcomeController extends Controller{
	#[Request(route:'/',method:RequestMethod::GET,accept:'text/html')]
	public static function index():void{
		try{
			I18n::load('common');
			I18n::load('public');
			$page = new PageContext();
			$page->title(I18n::t('public.welcome.title',['name'=>$_ENV['APP_NAME']]));
			$page->index('public-home');
			$view = new HtmlPresenter();
			$view->set('page',$page)
				->render('page/public/home.php');
		}catch(\Throwable){
			// logging
		}
	}
}