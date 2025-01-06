<?php

namespace App\Tests\Controller;

use App\Entity\Main;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class MainControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/main/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Main::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Main index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'main[title_main]' => 'Testing',
            'main[optional_content]' => 'Testing',
            'main[background_image]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Main();
        $fixture->setTitle_main('My Title');
        $fixture->setOptional_content('My Title');
        $fixture->setBackground_image('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Main');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Main();
        $fixture->setTitle_main('Value');
        $fixture->setOptional_content('Value');
        $fixture->setBackground_image('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'main[title_main]' => 'Something New',
            'main[optional_content]' => 'Something New',
            'main[background_image]' => 'Something New',
        ]);

        self::assertResponseRedirects('/main/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle_main());
        self::assertSame('Something New', $fixture[0]->getOptional_content());
        self::assertSame('Something New', $fixture[0]->getBackground_image());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Main();
        $fixture->setTitle_main('Value');
        $fixture->setOptional_content('Value');
        $fixture->setBackground_image('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/main/');
        self::assertSame(0, $this->repository->count([]));
    }
}
