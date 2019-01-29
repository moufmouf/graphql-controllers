<?php

namespace TheCodingMachine\GraphQLite\Mappers;

use Doctrine\Common\Annotations\AnnotationReader;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use Mouf\Picotainer\Picotainer;
use Symfony\Component\Cache\Simple\ArrayCache;
use Symfony\Component\Cache\Simple\NullCache;
use TheCodingMachine\GraphQLite\AbstractQueryProviderTest;
use TheCodingMachine\GraphQLite\Fixtures\Interfaces\ClassA;
use TheCodingMachine\GraphQLite\Fixtures\Interfaces\ClassB;
use TheCodingMachine\GraphQLite\Fixtures\Interfaces\ClassC;
use TheCodingMachine\GraphQLite\Fixtures\Interfaces\Types\ClassAType;
use TheCodingMachine\GraphQLite\Fixtures\Interfaces\Types\ClassBType;
use TheCodingMachine\GraphQLite\Fixtures\TestObject;
use TheCodingMachine\GraphQLite\NamingStrategy;
use TheCodingMachine\GraphQLite\TypeGenerator;
use TheCodingMachine\GraphQLite\Types\MutableObjectType;

class RecursiveTypeMapperTest extends AbstractQueryProviderTest
{

    public function testMapClassToType()
    {
        $objectType = new MutableObjectType([
            'name' => 'Foobar'
        ]);

        $typeMapper = new StaticTypeMapper();
        $typeMapper->setTypes([
            ClassB::class => $objectType
        ]);

        $recursiveTypeMapper = new RecursiveTypeMapper($typeMapper, new NamingStrategy(), new ArrayCache(), $this->getTypeRegistry());

        $this->assertFalse($typeMapper->canMapClassToType(ClassC::class));
        $this->assertTrue($recursiveTypeMapper->canMapClassToType(ClassC::class));
        $this->assertSame($objectType, $recursiveTypeMapper->mapClassToType(ClassC::class, null));

        $this->assertFalse($recursiveTypeMapper->canMapClassToType(ClassA::class));
        $this->expectException(CannotMapTypeException::class);
        $recursiveTypeMapper->mapClassToType(ClassA::class, null);
    }

    public function testMapNameToType()
    {
        $objectType = new MutableObjectType([
            'name' => 'Foobar'
        ]);

        $typeMapper = new StaticTypeMapper();
        $typeMapper->setTypes([
            ClassB::class => $objectType
        ]);

        $recursiveTypeMapper = new RecursiveTypeMapper($typeMapper, new NamingStrategy(), new ArrayCache(), $this->getTypeRegistry());

        $this->assertTrue($recursiveTypeMapper->canMapNameToType('Foobar'));
        $this->assertSame($objectType, $recursiveTypeMapper->mapNameToType('Foobar'));
    }

    public function testMapNameToType2()
    {
        $recursiveMapper = $this->getTypeMapper();

        $this->assertTrue($recursiveMapper->canMapNameToType('ClassA'));
        $this->assertTrue($recursiveMapper->canMapNameToType('ClassB'));
        $this->assertTrue($recursiveMapper->canMapNameToType('ClassAInterface'));
        $this->assertFalse($recursiveMapper->canMapNameToType('NotExists'));
        $this->assertSame('ClassA', $recursiveMapper->mapNameToType('ClassA')->name);
        $this->assertSame('ClassAInterface', $recursiveMapper->mapNameToType('ClassAInterface')->name);

        $this->expectException(CannotMapTypeException::class);
        $recursiveMapper->mapNameToType('NotExists');
    }


    public function testMapClassToInputType()
    {
        $inputObjectType = new InputObjectType([
            'name' => 'Foobar'
        ]);

        $typeMapper = new StaticTypeMapper();
        $typeMapper->setInputTypes([
            ClassB::class => $inputObjectType
        ]);

        $recursiveTypeMapper = new RecursiveTypeMapper($typeMapper, new NamingStrategy(), new ArrayCache(), $this->getTypeRegistry());

        $this->assertFalse($recursiveTypeMapper->canMapClassToInputType(ClassC::class));

        $this->expectException(CannotMapTypeException::class);
        $recursiveTypeMapper->mapClassToInputType(ClassC::class);
    }

    protected function getTypeMapper()
    {
        $container = new Picotainer([
            ClassAType::class => function() {
                return new ClassAType();
            },
            ClassBType::class => function() {
                return new ClassBType();
            }
        ]);

        $namingStrategy = new NamingStrategy();

        $typeGenerator = new TypeGenerator($this->getAnnotationReader(), $this->getControllerQueryProviderFactory(), $namingStrategy, $this->getTypeRegistry(), $this->getRegistry());

        $mapper = new GlobTypeMapper('TheCodingMachine\GraphQLite\Fixtures\Interfaces\Types', $typeGenerator, $this->getInputTypeGenerator(), $this->getInputTypeUtils(), $container, new \TheCodingMachine\GraphQLite\AnnotationReader(new AnnotationReader()), $namingStrategy, new NullCache());

        return new RecursiveTypeMapper($mapper, new NamingStrategy(), new ArrayCache(), $this->getTypeRegistry());
    }

    public function testMapClassToInterfaceOrType()
    {
        $recursiveMapper = $this->getTypeMapper();

        $type = $recursiveMapper->mapClassToInterfaceOrType(ClassA::class, null);
        $this->assertInstanceOf(InterfaceType::class, $type);
        $this->assertSame('ClassAInterface', $type->name);

        $classAObjectType = $recursiveMapper->mapClassToType(ClassA::class, null);
        $this->assertInstanceOf(ObjectType::class, $classAObjectType);
        $this->assertCount(1, $classAObjectType->getInterfaces());

        $type = $recursiveMapper->mapClassToInterfaceOrType(ClassC::class, null);
        $this->assertInstanceOf(ObjectType::class, $type);
        $this->assertSame('ClassB', $type->name);

        $interfaces = $recursiveMapper->findInterfaces(ClassC::class);
        $this->assertCount(1, $interfaces);
        $this->assertSame('ClassAInterface', $interfaces[0]->name);

        $classBObjectType = $recursiveMapper->mapClassToType(ClassC::class, null);
        $this->assertInstanceOf(ObjectType::class, $classBObjectType);
        $this->assertCount(1, $classBObjectType->getInterfaces());

        $this->expectException(CannotMapTypeException::class);
        $recursiveMapper->mapClassToInterfaceOrType('Not exists', null);
    }

    public function testGetOutputTypes()
    {
        $recursiveMapper = $this->getTypeMapper();

        $outputTypes = $recursiveMapper->getOutputTypes();
        $this->assertArrayHasKey('TheCodingMachine\\GraphQLite\\Fixtures\\Interfaces\\ClassA', $outputTypes);
        $this->assertArrayHasKey('TheCodingMachine\\GraphQLite\\Fixtures\\Interfaces\\ClassB', $outputTypes);
        $this->assertArrayNotHasKey('TheCodingMachine\\GraphQLite\\Fixtures\\Interfaces\\ClassC', $outputTypes);
    }


}
