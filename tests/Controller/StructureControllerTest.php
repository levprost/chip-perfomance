<?php

namespace App\Tests\Controller;

use App\Entity\Structure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class StructureControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/structure/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Structure::class);

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
        self::assertPageTitleContains('Structure index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'structure[image_structure]' => 'Testing',
            'structure[content_structure]' => 'Testing',
            'structure[main]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Structure();
        $fixture->setImage_structure('My Title');
        $fixture->setContent_structure('My Title');
        $fixture->setMain('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Structure');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Structure();
        $fixture->setImage_structure('Value');
        $fixture->setContent_structure('Value');
        $fixture->setMain('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'structure[image_structure]' => 'Something New',
            'structure[content_structure]' => 'Something New',
            'structure[main]' => 'Something New',
        ]);

        self::assertResponseRedirects('/structure/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getImage_structure());
        self::assertSame('Something New', $fixture[0]->getContent_structure());
        self::assertSame('Something New', $fixture[0]->getMain());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Structure();
        $fixture->setImage_structure('Value');
        $fixture->setContent_structure('Value');
        $fixture->setMain('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/structure/');
        self::assertSame(0, $this->repository->count([]));
    }
}
