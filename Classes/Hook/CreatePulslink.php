<?php

namespace UniPotsdam\Lsfapi\Hook;

/**
     * --------------------------------------------------------------
	 * This file is part of the package UniPotsdam\Orcid.
     * copyright 2020 by University Potsdam
     * https://www.uni-potsdam.de/
     *
     * Project: Orcid Extension
	 * User: Anuj Sharma (asharma@uni-potsdam.de)
     *
     * --------------------------------------------------------------
     */

    class CreatePulslink
    {
        //Create puls link with Course Id
        public function pulsLink($url, $crsId){

            return $url.$crsId;

        } 

    }