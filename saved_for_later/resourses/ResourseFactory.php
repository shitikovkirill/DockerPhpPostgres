<?php
/**
 * Created by PhpStorm.
 * User: kirill
 * Date: 05.04.16
 * Time: 11:59
 */

namespace Calendar\Resourses;

require_once __DIR__.'/factories/PopupFactory.php';
require_once __DIR__.'/factories/Popup2Factory.php';
require_once __DIR__.'/interfaces/Css.php';
require_once __DIR__.'/interfaces/Js.php';
require_once __DIR__.'/factories/ComponentFactory.php';

class ResourseFactory
{
    private static $instance;
    private $count = 0;

    private function __construct(){ }

    public function getFactory($name){
        $factory = null;
        switch($name){
            case 'Knocout':
                $factory = new KnockoutFactory();
                break;
            case 'Bootstrap':
                $factory = new BootstrapFactory();
                break;
            case 'Popup':
                $factory = new PopupFactory();
                break;
            case 'Popup2':
                $factory = new Popup2Factory();
                break;
        }
        return $factory;
    }

    public static function singleton()
    {
        if (!isset(self::$instance)) {
            //echo 'Создание нового экземпляра.';
            $className = __CLASS__;
            self::$instance = new $className;
        }
        return self::$instance;
    }

    public function increment()
    {
        return $this->count++;
    }

    public function __clone()
    {
        trigger_error('Клонирование запрещено.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Десериализация запрещена.', E_USER_ERROR);
    }
}