<?php

namespace Palladium\Entity;

use PHPUnit\Framework\TestCase;
use Palladium\Exception\InvalidCookieToken;

/**
 * @covers Palladium\Entity\CookieIdentity
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class CookieIdentityTest extends TestCase
{

    /**
     * @test
     * @dataProvider Provide_Assignment_of_Params
     */
    public function Assignment_of_Series($param, $expected)
    {
        $instance = new CookieIdentity;

        $instance->setSeries($param);
        $this->assertSame($expected, $instance->getSeries());
    }


    public function Provide_Assignment_of_Params()
    {
        return [
            [null, null],
            [234, '234'],
            [0, '0'],
            ['alpha', 'alpha'],
        ];
    }


    /**
     * @test
     * @dataProvider Provide_Assignment_of_Params
     */
    public function Assignment_of_Key($param, $expected)
    {
        $instance = new CookieIdentity;

        $instance->setKey($param);
        $this->assertSame($expected, $instance->getKey());
    }


    /**
     * @test
     * @dataProvider Provide_Assignment_of_Params
     */
    public function Assignment_of_Hash($param, $expected)
    {
        $instance = new CookieIdentity;

        $instance->setHash($param);
        $this->assertSame($expected, $instance->getHash());
    }

    /** @test */
    public function Retrieval_of_Fingerprint()
    {
        $instance = new CookieIdentity;
        $instance->setSeries('alpha');

        $this->assertSame(
            '9cc3c0f06e170b14d7c52a8cbfc31bf9e4cc491e2aa9b79a385bcffa62f6bc619fcc95b5c1eb933dfad9c281c77208af',
            $instance->getFingerprint()
        );
    }

    /** @test */
    public function Hash_Creation_and_Verification_Integriety()
    {
        $instance = new CookieIdentity;
        $instance->setKey('alpha');
        $this->assertTrue($instance->matchKey('alpha'));

        $hash = $instance->getHash();

        $instance = new CookieIdentity;
        $instance->setHash($hash);
        $this->assertTrue($instance->matchKey('alpha'));
    }

    /** @test */
    public function Generation_of_New_Random_Key()
    {
        $instance = new CookieIdentity;
        $this->assertNull($instance->getKey());
        $this->assertNull($instance->getHash());

        $instance->generateNewKey();
        $this->assertNotNull($instance->getKey());
        $this->assertNotNull($instance->getHash());
    }

    /** @test */
    public function Generation_of_New_Random_Series()
    {
        $instance = new CookieIdentity;
        $this->assertNull($instance->getSeries());

        $instance->generateNewSeries();
        $this->assertNotNull($instance->getSeries());
    }
}
