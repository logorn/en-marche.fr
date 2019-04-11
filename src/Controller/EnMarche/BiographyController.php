<?php

namespace AppBundle\Controller\EnMarche;

use AppBundle\Entity\Biography\ExecutiveOfficeMember;
use AppBundle\Repository\Biography\ExecutiveOfficeMemberRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class BiographyController extends Controller
{
    /**
     * @Route("/le-mouvement/notre-organisation", name="app_our_organization", methods={"GET"})
     */
    public function executiveOfficeMemberListAction(ExecutiveOfficeMemberRepository $repository): Response
    {
        $allMembers = $repository->findAllPublishedMembers();

        return $this->render('page/le-mouvement/notre-organisation.html.twig', [
            'executiveOfficeMembers' => $allMembers->getExecutiveOfficeMembers(),
            'executiveOfficer' => $allMembers->getExecutiveOfficer(),
        ]);
    }

    /**
     * @Route("/le-mouvement/notre-organisation/{slug}", name="app_our_organization_show", methods={"GET"})
     * @Entity("executiveOfficeMember", expr="repository.findOnePublishedBySlug(slug)")
     */
    public function executiveOfficeMemberAction(ExecutiveOfficeMember $executiveOfficeMember): Response
    {
        return $this->render('biography/executive_office_member/show.html.twig', [
            'executiveOfficeMember' => $executiveOfficeMember,
        ]);
    }
}
