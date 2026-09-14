<?php declare(strict_types=1);
namespace Frrame\Test\Unit;
use PHPUnit\Framework\TestCase;
use Frrame\Facade\PageFacade;
class PageFacadeTest extends TestCase{
    public function testTitleDefaultsThenOverrides():void{
        $page = new PageFacade();
        $this->assertSame('Title', $page->title());
        $page->title('Welcome');
        $this->assertSame('Welcome', $page->title());
    }
    public function testIndexIsAndIndexAre():void{
        $page = new PageFacade(['INDEX' => 'admin/user/edit']);
        $this->assertTrue($page->indexIs('admin/user/edit'));
        $this->assertTrue($page->indexAre('admin/user'));
        $this->assertFalse($page->indexAre('public'));
    }
}
