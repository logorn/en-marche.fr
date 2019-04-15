<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\ReferentOrganizationalChart\AbstractOrganizationalChartItem;
use AppBundle\Entity\ReferentOrganizationalChart\GroupOrganizationalChartItem;
use AppBundle\Entity\ReferentOrganizationalChart\PersonOrganizationalChartItem;
use AppBundle\Entity\ReferentOrganizationalChart\ReferentPersonLink;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadOrganizationalChartItemData extends Fixture
{
    public const MAPPING = [
        'referent' => [
            'class' => GroupOrganizationalChartItem::class,
            'label' => 'Référent départemental',
            'children' => [
                'co_referent' => [
                    'class' => PersonOrganizationalChartItem::class,
                    'label' => 'Co-Référent',
                    'referent_person_link' => [
                        'firstName' => 'Jean',
                        'lastName' => 'Dupont',
                        'email' => 'jean-dupont@test.fr',
                        'phone' => '06 23 45 67 89',
                        'postalAddress' => '9 rue du Lycée, Nice',
                        'referent' => 'referent3',
                    ],
                    'children' => [
                        'resp_land' => [
                            'class' => GroupOrganizationalChartItem::class,
                            'label' => 'Responsables territoriaux',
                            'children' => [
                                'resp_mobi' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable mobilisation',
                                    'children' => [
                                        'resp_mobi_bis' => [
                                            'class' => PersonOrganizationalChartItem::class,
                                            'label' => 'Responsable mobilisation',
                                            'children' => [
                                                'resp_mobi_bis_bis' => [
                                                    'class' => PersonOrganizationalChartItem::class,
                                                    'label' => 'Responsable mobilisation',
                                                ],
                                                'resp_local_committee_bis_bis' => [
                                                    'class' => PersonOrganizationalChartItem::class,
                                                    'label' => 'Responsable comités locaux',
                                                ],
                                            ],
                                        ],
                                        'resp_local_committee_bis' => [
                                            'class' => PersonOrganizationalChartItem::class,
                                            'label' => 'Responsable comités locaux',
                                        ],
                                    ],
                                ],
                                'resp_local_committee' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable comités locaux',
                                ],
                            ],
                        ],
                        'resp_admin' => [
                            'class' => GroupOrganizationalChartItem::class,
                            'label' => 'Responsables administratif',
                            'children' => [
                                'resp_logi' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable logistique',
                                ],
                                'resp_finance' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable Financier',
                                ],
                                'resp_politic' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable Politique',
                                ],
                            ],
                        ],
                        'resp_com_digital' => [
                            'class' => GroupOrganizationalChartItem::class,
                            'label' => 'Responsables communication et digitaux',
                            'children' => [
                                'resp_com' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable communication',
                                ],
                                'resp_content' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable contenus',
                                ],
                                'resp_digital' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable digital',
                                    'referent_person_link' => [
                                        'firstName' => 'Carl',
                                        'lastName' => 'Mirabeau',
                                        'email' => 'carl999@example.fr',
                                        'phone' => '06 23 45 67 89',
                                        'postalAddress' => '122 rue de Mouxy, Mouxy',
                                        'referent' => 'referent3',
                                    ],
                                ],
                            ],
                        ],
                        'resp_themes' => [
                            'class' => GroupOrganizationalChartItem::class,
                            'label' => 'Responsables Thématiques',
                            'children' => [
                                'resp_eu' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable Europe',
                                ],
                                'resp_theme' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable thématique',
                                ],
                                'resp_citizen_engagement' => [
                                    'class' => PersonOrganizationalChartItem::class,
                                    'label' => 'Responsable Engagement Citoyen',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    public function load(ObjectManager $manager)
    {
        $this->createTree($manager, static::MAPPING);

        $manager->flush();
    }

    private function createTree(
        ObjectManager $manager,
        array $mapping,
        AbstractOrganizationalChartItem $parent = null
    ): void {
        foreach ($mapping as $item) {
            $this->createItem($manager, $item, $parent);
        }
    }

    private function createItem(
        ObjectManager $manager,
        array $item,
        AbstractOrganizationalChartItem $parent = null
    ): AbstractOrganizationalChartItem {
        /** @var AbstractOrganizationalChartItem $orgaChartItem */
        $orgaChartItem = new $item['class']($item['label'], $parent);
        if (isset($item['referent_person_link'])) {
            /** @var string[] $refItems */
            $refItems = $item['referent_person_link'];
            /** @var PersonOrganizationalChartItem $orgaChartItem */
            $referentPersonLink = new ReferentPersonLink($orgaChartItem, $this->getReference($refItems['referent']));
            unset($refItems['referent']);
            foreach ($refItems as $key => $value) {
                $referentPersonLink->{'set'.ucfirst($key)}($value);
            }
            $manager->persist($referentPersonLink);
        }
        $manager->persist($orgaChartItem);
        if (isset($item['children'])) {
            $this->createTree($manager, $item['children'], $orgaChartItem);
        }

        return $orgaChartItem;
    }

    public function getDependencies()
    {
        return [
            LoadReferentData::class,
        ];
    }
}
