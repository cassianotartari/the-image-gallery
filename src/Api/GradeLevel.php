<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Api;

/**
 * Description of GradeLevel
 *
 * @author cassiano
 */
class GradeLevel {
    const FR = "FR";
    const SPH = "SPH";
    const JR = "JR";
    const SR = "SR";
    
    public static function getGradeLevelNumber($gradeLevelTxt) {
        switch ($gradeLevelTxt) {
            default:
                return null;
            case $this::FR:
                return 9;
            case $this::SPH:
                return 10;
            case $this::JR:
                return 11;
            case $this::SR:
                return 12;
        }
    }
}
