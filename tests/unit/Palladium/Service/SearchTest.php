<?php

namespace Palladium\Service;

use PHPUnit\Framework\TestCase;

use Psr\Log\LoggerInterface;
use Palladium\Repository\Identity as Repository;
use Palladium\Contract\HasId;

use Palladium\Entity;
use Palladium\Mapper;
use Palladium\Exception\IdentityNotFound;

/**
 * @covers Palladium\Service\Search
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
final class SearchTest extends TestCase
{
    /** @test */
    public function Looking_for_Identity_by_Id()
    {
        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function(HasId $entity) {
                $entity->setAccountId(1);
            }));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $this->assertInstanceOf(
            Entity\Identity::class,
            $instance->findIdentityById(12121)
        );
    }

    /** @test */
    public function Failure_to_Find_Identity_by_Id()
    {
        $this->expectException(IdentityNotFound::class);

        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository->expects($this->once())->method('load');

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $instance->findIdentityById(42);
    }

    /** @test */
    public function Looking_for_Email_Identity_by_Identifier()
    {
        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function(HasId $entity) {
                $entity->setId(1);
            }));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $this->assertInstanceOf(
            Entity\StandardIdentity::class,
            $instance->findStandardIdentityByIdentifier('foo@example.com')
        );
    }

    /** @test */
    public function Failure_to_Find_Email_Identity_by_Identifier()
    {
        $this->expectException(IdentityNotFound::class);

        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository->expects($this->once())->method('load');

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $instance->findStandardIdentityByIdentifier('foo@example.com');
    }

    /** @test */
    public function Looking_for_Email_Identity_by_Token()
    {
        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->exactly(2))
            ->method('load')
            ->will($this->returnCallback(function(HasId $entity) {
                $entity->setId(1);
            }));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $this->assertInstanceOf(
            Entity\StandardIdentity::class,
            $instance->findStandardIdentityByToken('12345678901234567890123456789012', Entity\Identity::ACTION_NONE)
        );
    }

    /** @test */
    public function Failure_to_Find_Email_Identity_by_Token()
    {
        $this->expectException(IdentityNotFound::class);

        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository->expects($this->once())->method('load');

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $this->assertInstanceOf(
            Entity\StandardIdentity::class,
            $instance->findStandardIdentityByToken('12345678901234567890123456789012', Entity\Identity::ACTION_NONE)
        );
    }


    /** @test */
    public function Looking_for_Cookie_Identity()
    {
        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function(HasId $entity) {
                $entity->setId(1);
            }));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $this->assertInstanceOf(
            Entity\CookieIdentity::class,
            $instance->findCookieIdentity(123, '12345678901234567890123456789012')
        );
    }

    /** @test */
    public function Failure_to_Find_Cookie_Identity()
    {
        $this->expectException(IdentityNotFound::class);

        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function() {}));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $this->assertInstanceOf(
            Entity\CookieIdentity::class,
            $instance->findCookieIdentity(123, '12345678901234567890123456789012')
        );
    }

    /** @test */
    public function Looking_for_Identity_with_Given_Account_Id()
    {

        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function($collection) {
                $collection->addBlueprint(['id' => 1]);
                $collection->addBlueprint(['id' => 2]);
            }));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $list = $instance->findIdentitiesByAccountId(4);
        $this->assertSame([1, 2], $list->getIds());
    }

    /** @test */
    public function Looking_for_Identity_with_Given_Parent_Id()
    {
        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function($collection) {
                $collection->addBlueprint(['id' => 7]);
                $collection->addBlueprint(['id' => 3]);
            }));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $list = $instance->findIdentitiesByParentId(123);
        $this->assertSame([3, 7], $list->getIds());
    }

    /** @test */
    public function Looking_for_OneTime_Identity()
    {
        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function(HasId $entity) {
                $entity->setId(1);
            }));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $this->assertInstanceOf(
            Entity\NonceIdentity::class,
            $instance->findNonceIdentityByIdentifier('qwerty')
        );
    }

    /** @test */
    public function Failure_to_Find_OneTime_Identity()
    {
        $this->expectException(IdentityNotFound::class);

        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function() {}));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $this->assertInstanceOf(
            Entity\NonceIdentity::class,
            $instance->findNonceIdentityByIdentifier('qwerty')
        );
    }

    /** @test */
    public function locate_Standard_Identity_by_Id()
    {
        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function(HasId $entity) {
                $entity->setAccountId(2);
            }));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $identity = $instance->findStandardIdentityById(4);


        $this->assertInstanceOf(Entity\StandardIdentity::class, $identity);
        $this->assertSame(2, $identity->getAccountId());
    }

    /** @test */
    public function exception_on_Failure_to_find_Standard_identity_by_Id()
    {
        $this->expectException(IdentityNotFound::class);

        $repository = $this
                    ->getMockBuilder(Repository::class)
                    ->disableOriginalConstructor()
                    ->getMock();
        $repository
            ->expects($this->once())
            ->method('load')
            ->will($this->returnCallback(function() {}));

        $instance = new Search(
            $repository,
            $this->getMockBuilder(LoggerInterface::class)->getMock()
        );

        $identity = $instance->findStandardIdentityById(4);
    }
}
