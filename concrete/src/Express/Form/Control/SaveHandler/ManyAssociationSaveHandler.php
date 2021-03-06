<?php
namespace Concrete\Core\Express\Form\Control\SaveHandler;

use Concrete\Core\Entity\Express\Control\AssociationControl;
use Concrete\Core\Entity\Express\Control\Control;
use Concrete\Core\Entity\Express\Entry;
use Concrete\Core\Express\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;

class ManyAssociationSaveHandler implements SaveHandlerInterface
{
    protected $entityManager;

    public function __construct(EntityManager $manager)
    {
        $this->entityManager = $manager;
    }

    public function saveFromRequest(Control $control, Entry $entry, Request $request)
    {
        /**
         * @var $control AssociationControl
         */
        $r = $this->entityManager->getRepository('Concrete\Core\Entity\Express\Entry');
        $entityIds = $request->request->get('express_association_' . $control->getId());
        $association = new Entry\ManyAssociation();
        $association->setAssociation($control->getAssociation());
        $association->setEntry($entry);
        if (is_array($entityIds)) {
            foreach($entityIds as $entityId) {
                $associatedEntry = $r->findOneById($entityId);
                $target = $control->getAssociation()->getTargetEntity();
                if (is_object($associatedEntry) && $associatedEntry->getEntity()->getID() == $target->getID()) {
                    $association->getSelectedEntries()->add($associatedEntry);
                }
            }
        }
        $this->entityManager->persist($association);
        $this->entityManager->flush();

    }
}
