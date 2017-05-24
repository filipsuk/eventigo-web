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

    protected function setUp()
    {
        $container = (new ContainerFactory)->create();
        $this->newsletterService = $container->getByType(NewsletterService::class);
        $this->linkGenerator = $container->getByType(LinkGenerator::class);
    }

    public function testRedirectLinks()
    {
        $this->assertSame('https://eventigo.cz/redirect/?url=someUrl', $this->linkGenerator->link('Front:Redirect:', ['someUrl']));
    }

    public function testPrepareLinks()
    {
        $this->assertSame('https://eventigo.cz', $this->newsletterService->context->parameters['baseUrl']);

        $templateData = [];
        $templateData = $this->newsletterService->prepareLinks(
            $templateData, 'userToken', 'baseUrl', 'newsletterHash'
        );

        $this->assertSame([
            'updatePreferencesUrl' => 'https://eventigo.cz/profile/settings/userToken?utm_campaign=newsletterButton&utm_source=newsletter&utm_medium=email',
            'feedUrl' => 'https://eventigo.cz/?token=userToken&utm_campaign=newsletterButton&utm_source=newsletter&utm_medium=email',
            'unsubscribeUrl' => 'baseUrl/newsletter/unsubscribe/newsletterHash'
        ], $templateData);
    }
}