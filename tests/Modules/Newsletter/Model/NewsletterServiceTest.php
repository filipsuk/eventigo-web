<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model;

use App\Tests\ContainerFactory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class NewsletterServiceTest extends TestCase
{
    /**
     * @var NewsletterService
     */
    private $newsletterService;

    protected function setUp()
    {
        $container = (new ContainerFactory)->create();
        $this->newsletterService = $container->getByType(NewsletterService::class);
    }

    public function testBuildArrayForTemplate()
    {
        $this->expectException(RuntimeException::class);
        $arrayForTemplate = $this->newsletterService->buildArrayForTemplate(1);
        $this->assertSame([], $arrayForTemplate);
    }
}