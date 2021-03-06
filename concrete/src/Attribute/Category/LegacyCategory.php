<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Attribute\AttributeValueInterface;
use Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexerInterface;
use Concrete\Core\Attribute\Key\RequestLoader\StandardRequestLoader;
use Concrete\Core\Entity\Attribute\Key\FileKey;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\Attribute\Key\LegacyKey;
use Concrete\Core\Entity\Attribute\Type;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class LegacyCategory implements CategoryInterface, StandardSearchIndexerInterface
{

    use StandardCategoryTrait;

    public function __construct(Application $application, EntityManager $entityManager)
    {
        $this->application = $application;
        $this->entityManager = $entityManager;
    }

    public function getEntityManager()
    {
        return $this->entityManager;
    }

    protected function getLegacyKeyClass()
    {
        $class = camelcase($this->getCategoryEntity()->getAttributeKeyCategoryHandle());
        $prefix = ($this->getCategoryEntity()->getPackageID() > 0) ?
            $this->getCategoryEntity()->getPackageHandle() : false;
        $class = core_class('Core\\Attribute\\Key\\' . $class . 'Key', $prefix);
        return $class;
    }

    public function getSearchIndexer()
    {
        /*
        $indexer = $this->application->make('Concrete\Core\Attribute\Category\SearchIndexer\StandardSearchIndexer');

        return $indexer;
        */
        return false;
    }

    public function getIndexedSearchTable()
    {
        $class = $this->getLegacyKeyClass();
        if (method_exists($class, 'getIndexedSearchTable')) {
            return $class::getIndexedSearchTable();
        }
    }

    public function getIndexedSearchPrimaryKeyValue($mixed)
    {
        return false;
    }

    public function getSearchIndexFieldDefinition()
    {
        $class = $this->getLegacyKeyClass();
        return $class::getSearchIndexFieldDefinition();
    }

    public function getList()
    {
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Attribute\Key\LegacyKey');
        return $r->findBy(array(
            'category' => $this->getCategoryEntity(),
            'akIsSearchable' => true,
            'akIsInternal' => false,
        ));
    }

    public function getAttributeValues($mixed)
    {
        $arguments = func_get_args();
        return call_user_func_array(array($this->getLegacyKeyClass(), 'getAttributes'), $arguments);
    }

    public function addFromRequest(\Concrete\Core\Entity\Attribute\Type $type, Request $request)
    {
        // TODO: Implement addFromRequest() method.
    }

    public function updateFromRequest(Key $key, Request $request)
    {
        $previousHandle = $key->getAttributeKeyHandle();

        $loader = new StandardRequestLoader();
        $loader->load($key, $request);

        $controller = $key->getController();
        $key_type = $controller->saveKey($request->request->all());
        if (!is_object($key_type)) {
            $key_type = $controller->getAttributeKeyType();
        }
        $key_type->setAttributeKey($key);
        $key->setAttributeKeyType($key_type);

        // Modify the category's search indexer.
        $indexer = $this->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->updateRepository($this, $key, $previousHandle);
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();

        return $key;
    }

    public function getAttributeKeyByID($akID)
    {
        // TODO: Implement getAttributeKeyByID() method.
    }

    public function deleteKey(Key $key)
    {
        $controller = $key->getController();
        $controller->deleteKey();

        // Delete from any attribute sets
        $r = $this->entityManager->getRepository('\Concrete\Core\Entity\Attribute\SetKey');
        $setKeys = $r->findBy(array('attribute_key' => $key));
        foreach ($setKeys as $setKey) {
            $this->entityManager->remove($setKey);
        }
        $this->entityManager->remove($key);

        $this->entityManager->remove($key);
        $this->entityManager->flush();
    }

    public function deleteValue(AttributeValueInterface $value)
    {
        // TODO: Implement deleteValue() method.
    }

    public function getAttributeValue(Key $key, $mixed)
    {
        // TODO: Implement getAttributeValue() method.
    }

    public function addAttributeKey($type, $args, $pkg = false)
    {
        if (!is_object($type)) {
            $type = \Concrete\Core\Attribute\Type::getByHandle($type);
        }

        $key_type = $type->getController()->getAttributeKeyType();
        // $key is actually an array.
        $handle = $args['akHandle'];
        $name = $args['akName'];
        $key = new LegacyKey();
        $key->setAttributeKeyHandle($handle);
        $key->setAttributeKeyName($name);
        $key_type->setAttributeKey($key);

        $key->setAttributeKeyType($key_type);
        $key->setAttributeCategory($this->getCategoryEntity());

        if (is_object($pkg)) {
            $key->setPackage($pkg);
        }

        // Modify the category's search indexer.
        $indexer = $this->getSearchIndexer();
        if (is_object($indexer)) {
            $indexer->updateRepository($this, $key);
        }

        $this->entityManager->persist($key);
        $this->entityManager->flush();
        return $key;
    }



}
