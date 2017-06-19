<?php declare(strict_types=1);

namespace App\Modules\Newsletter\Model;

use App\Tests\ContainerFactory;
use Nette\Application\LinkGenerator;
use PHPUnit\Framework\TestCase;

final class NewsletterServiceTest extends TestCase
{
    /**
     * @var NewsletterService
     */
    private $newsletterService;

    /**
     * @var LinkGenerator
     */
    private $linkGenerator;

    /**
     * @var string
     */
    private $baseUrl;

    protected function setUp()
    {
        $container = (new ContainerFactory)->create();
        $this->newsletterService = $container->getByType(NewsletterService::class);
        $this->linkGenerator = $container->getByType(LinkGenerator::class);

        $this->baseUrl = $container->getParameters()['baseUrl'];
    }

    public function testRedirectLinks()
    {
        $this->assertSame(
            $this->baseUrl . '/redirect/?url=someUrl',
            $this->linkGenerator->link('Front:Redirect:', ['someUrl'])
        );
    }

    public function testPrepareLinks()
    {
        $templateData = [];
        $templateData = $this->newsletterService->prepareLinks(
            $templateData, 'userToken', 'baseUrl', 'newsletterHash'
        );

        $this->assertSame([
            'updatePreferencesUrl' => $this->baseUrl . '/profile/settings/userToken?utm_campaign=newsletterButton&utm_source=newsletter&utm_medium=email',
            'feedUrl' => $this->baseUrl . '/?token=userToken&utm_campaign=newsletterButton&utm_source=newsletter&utm_medium=email',
            'unsubscribeUrl' => 'baseUrl/newsletter/unsubscribe/newsletterHash'
        ], $templateData);
    }
}