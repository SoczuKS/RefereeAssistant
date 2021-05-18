<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class MenuBuilder
{
    private $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function mainMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('home', ['route' => 'main']);
        $menu->addChild('matches', ['route' => 'matches']);
        $menu->addChild('tournaments', ['route' => 'tournaments']);
        $menu->addChild('competitions', ['route' => 'competitions']);
        $menu->addChild('referees', ['route' => 'referees']);
        $menu->addChild('teams', ['route' => 'teams']);
        $menu->addChild('rounds', ['route' => 'rounds']);

        return $menu;
    }
}