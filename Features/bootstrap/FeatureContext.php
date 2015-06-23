<?php

/*
 * This file is part of the LoopBackApiBundle package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\SchemaTool;
use Fidry\LoopBackApiBundle\Tests\TestBundle\Entity\Dummy;

/**
 * Class FeatureContext.
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
class FeatureContext implements  Context
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $manager;

    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->manager = $doctrine->getManager();
        $this->schemaTool = new SchemaTool($this->manager);
        $this->classes = $this->manager->getMetadataFactory()->getAllMetadata();
    }

    /**
     * @BeforeScenario @createSchema
     */
    public function createDatabase()
    {
        $this->schemaTool->createSchema($this->classes);
    }

    /**
     * @AfterScenario @dropSchema
     */
    public function dropDatabase()
    {
        $this->schemaTool->dropSchema($this->classes);
    }

    /**
     * @Given there is :nb dummy objects
     */
    public function thereIsDummyObjects($nbr)
    {
        for ($i = 1; $i <= $nbr; ++$i) {
            $dummy = new Dummy();
            $dummy->setName('Dummy #'.$i);
            $dummy->setAlias('Alias #'.($nbr - $i));

            $this->manager->persist($dummy);
        }

        $this->manager->flush();
    }
}
