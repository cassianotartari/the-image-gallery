<?php

namespace Api;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DestinationApi
 *
 * @author cassiano
 */
class DestinationApi {
    
    /**
     * @var string
     */
    private $title;

    /**
     * @var integer
     */
    private $grade;

    /**
     * @var string
     */
    private $srcid;
    
    /**
     *
     * @var Application
     */
    private $app;
    
    public function getTitle() {
        return $this->title;
    }

    public function getGrade() {
        return $this->grade;
    }

    public function getSrcid() {
        return $this->srcid;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function setGrade($grade) {
        $this->grade = $grade;
    }

    public function setSrcid($srcid) {
        $this->srcid = $srcid;
    }

    public function __construct(Application $app) {
        $this->app = $app;
    }
    
    public function createCourse(Array $corses) {
        if($corses instanceof Course) {
            $this->app->get('/school/{schoolid}/courses', function($schoolid, Request $request) use ($app) {
                    
//            ...
        });
        }
        
        
    }

}
